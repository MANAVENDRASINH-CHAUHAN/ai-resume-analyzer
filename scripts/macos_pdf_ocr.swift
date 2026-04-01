import Foundation
import AppKit
import PDFKit
import Vision

guard CommandLine.arguments.count >= 2 else {
    fputs("Usage: swift macos_pdf_ocr.swift <pdf-path>\n", stderr)
    exit(1)
}

let pdfPath = CommandLine.arguments[1]
let pdfUrl = URL(fileURLWithPath: pdfPath)

guard let document = PDFDocument(url: pdfUrl) else {
    fputs("Could not open PDF file.\n", stderr)
    exit(1)
}

func buildPageImage(from page: PDFPage) -> CGImage? {
    let pageBounds = page.bounds(for: .mediaBox)
    let scale: CGFloat = 2.0
    let imageSize = NSSize(width: pageBounds.width * scale, height: pageBounds.height * scale)

    let image = NSImage(size: imageSize)

    image.lockFocus()
    NSColor.white.set()
    NSRect(origin: .zero, size: imageSize).fill()

    guard let context = NSGraphicsContext.current?.cgContext else {
        image.unlockFocus()
        return nil
    }

    context.scaleBy(x: scale, y: scale)
    page.draw(with: .mediaBox, to: context)
    image.unlockFocus()

    guard let imageData = image.tiffRepresentation,
          let imageRep = NSBitmapImageRep(data: imageData) else {
        return nil
    }

    return imageRep.cgImage
}

func extractPageText(from page: PDFPage) -> String {
    guard let cgImage = buildPageImage(from: page) else {
        return ""
    }

    let request = VNRecognizeTextRequest()
    request.recognitionLevel = .accurate
    request.usesLanguageCorrection = true
    request.recognitionLanguages = ["en-US"]

    let handler = VNImageRequestHandler(cgImage: cgImage, options: [:])

    do {
        try handler.perform([request])
    } catch {
        return ""
    }

    let observations = (request.results as? [VNRecognizedTextObservation]) ?? []

    let orderedLines = observations
        .compactMap { observation -> (text: String, box: CGRect)? in
            guard let candidate = observation.topCandidates(1).first else {
                return nil
            }

            return (candidate.string, observation.boundingBox)
        }
        .sorted { left, right in
            if abs(left.box.maxY - right.box.maxY) > 0.025 {
                return left.box.maxY > right.box.maxY
            }

            return left.box.minX < right.box.minX
        }
        .map(\.text)

    return orderedLines.joined(separator: "\n")
}

var pages: [String] = []

for index in 0..<document.pageCount {
    guard let page = document.page(at: index) else {
        continue
    }

    let pageText = extractPageText(from: page)

    if !pageText.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty {
        pages.append(pageText)
    }
}

print(pages.joined(separator: "\n\n"))
