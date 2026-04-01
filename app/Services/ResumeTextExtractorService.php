<?php

namespace App\Services;

use App\Models\Resume;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

class ResumeTextExtractorService
{
    public function extract(Resume $resume, ?string $manualText = null): array
    {
        $manualText = $this->normalizeText($manualText ?? '');

        if ($manualText !== '') {
            return [
                'text' => $manualText,
                'source' => 'manual_text',
                'used_manual_text' => true,
            ];
        }

        if (! Storage::disk('public')->exists($resume->file_path)) {
            throw new RuntimeException('Resume file was not found in storage.');
        }

        $fullPath = Storage::disk('public')->path($resume->file_path);
        $extension = strtolower($resume->file_type ?: pathinfo($fullPath, PATHINFO_EXTENSION));

        $text = match ($extension) {
            'docx' => $this->extractFromDocx($fullPath),
            'pdf' => $this->extractFromPdf($fullPath),
            'doc' => $this->extractFromDoc($fullPath),
            'txt' => $this->extractFromText($fullPath),
            default => '',
        };

        return [
            'text' => $this->normalizeText($text),
            'source' => $extension,
            'used_manual_text' => false,
        ];
    }

    protected function extractFromText(string $fullPath): string
    {
        return (string) file_get_contents($fullPath);
    }

    protected function extractFromDocx(string $fullPath): string
    {
        if (! class_exists(ZipArchive::class)) {
            return '';
        }

        $zip = new ZipArchive();

        if ($zip->open($fullPath) !== true) {
            return '';
        }

        $documentXml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        $documentXml = str_replace(['</w:p>', '</w:br>', '</w:tab>'], ["\n", "\n", ' '], $documentXml);

        return strip_tags($documentXml);
    }

    protected function extractFromPdf(string $fullPath): string
    {
        $content = (string) file_get_contents($fullPath);
        preg_match_all('/\(([^()]*)\)/s', $content, $matches);

        $text = implode(' ', $matches[1] ?? []);
        $text = str_replace(['\\n', '\\r', '\\t'], ' ', $text);
        $text = preg_replace('/\\\\([0-9]{3})/', ' ', $text) ?? $text;

        if ($this->hasUsefulText($text)) {
            return $text;
        }

        $ocrText = $this->extractFromPdfUsingMacOcr($fullPath);

        return $this->hasUsefulText($ocrText) ? $ocrText : $text;
    }

    protected function extractFromDoc(string $fullPath): string
    {
        $content = (string) file_get_contents($fullPath);
        $content = str_replace("\0", ' ', $content);

        return preg_replace('/[^A-Za-z0-9@\.\,\-\+\(\)\/:\s]/', ' ', $content) ?? '';
    }

    protected function normalizeText(string $text): string
    {
        $text = $this->sanitizeEncoding($text);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/u', ' ', $text) ?? $text;
        $text = preg_replace("/[ \t]+/", ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }

    protected function sanitizeEncoding(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $detectedEncoding = mb_detect_encoding($text, ['UTF-8', 'ASCII', 'ISO-8859-1', 'Windows-1252'], true);

        if ($detectedEncoding) {
            $text = mb_convert_encoding($text, 'UTF-8', $detectedEncoding);
        }

        $convertedText = @iconv('UTF-8', 'UTF-8//IGNORE', $text);

        return $convertedText !== false ? $convertedText : $text;
    }

    protected function hasUsefulText(string $text): bool
    {
        $text = trim($text);

        if (mb_strlen($text) < 80) {
            return false;
        }

        $alphaNumericCount = preg_match_all('/[A-Za-z0-9]/u', $text);

        return $alphaNumericCount !== false && $alphaNumericCount >= 40;
    }

    protected function extractFromPdfUsingMacOcr(string $fullPath): string
    {
        if (PHP_OS_FAMILY !== 'Darwin') {
            return '';
        }

        $scriptPath = base_path('scripts/macos_pdf_ocr.swift');

        if (! is_file($scriptPath) || ! is_file($fullPath)) {
            return '';
        }

        $command = sprintf(
            '/usr/bin/swift %s %s 2>/dev/null',
            escapeshellarg($scriptPath),
            escapeshellarg($fullPath)
        );

        $output = shell_exec($command);

        return is_string($output) ? trim($output) : '';
    }
}
