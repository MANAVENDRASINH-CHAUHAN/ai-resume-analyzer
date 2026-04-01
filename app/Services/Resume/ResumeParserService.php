<?php

namespace App\Services\Resume;

use App\Models\Resume;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class ResumeParserService extends BaseResumeService
{
    public function parse(Resume $resume, Collection $masterSkills): array
    {
        $filePath = Storage::disk('public')->path($resume->file_path);
        $extractedText = $this->extractTextFromFile($filePath, $resume->file_extension);
        $normalisedText = $this->normaliseText($extractedText);
        $lines = $this->prepareLines($normalisedText);
        $sections = $this->extractSections($lines);
        $detectedSkills = $this->detectSkills($normalisedText, $masterSkills);

        $educationText = $this->sectionOrKeywordText(
            $sections['education'] ?? [],
            $lines,
            $this->config['education_keywords']
        );

        $experienceText = $this->sectionOrKeywordText(
            $sections['experience'] ?? [],
            $lines,
            $this->config['experience_keywords']
        );

        $projectsText = $this->sectionOrKeywordText(
            $sections['projects'] ?? [],
            $lines,
            $this->config['project_keywords']
        );

        return [
            'full_text' => $normalisedText,
            'candidate_name' => $this->detectName($lines, $resume->user->name),
            'candidate_email' => $this->detectEmail($normalisedText),
            'candidate_phone' => $this->detectPhone($normalisedText),
            'education_text' => $educationText,
            'experience_text' => $experienceText,
            'projects_text' => $projectsText,
            'skills_text' => implode(', ', $detectedSkills),
            'detected_skills' => $detectedSkills,
            'sections' => $sections,
            'education_found' => $educationText !== '',
            'experience_found' => $experienceText !== '',
            'projects_found' => $projectsText !== '',
        ];
    }

    protected function extractTextFromFile(string $path, string $extension): string
    {
        $extension = strtolower($extension);

        return match ($extension) {
            'docx' => $this->extractDocxText($path),
            'doc' => $this->extractBinaryDocumentText($path),
            'pdf' => $this->extractPdfText($path),
            default => $this->extractBinaryDocumentText($path),
        };
    }

    protected function extractDocxText(string $path): string
    {
        if (! class_exists(ZipArchive::class)) {
            return $this->extractBinaryDocumentText($path);
        }

        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            return '';
        }

        $content = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        $content = str_replace(['</w:p>', '</w:tr>', '</w:tc>'], "\n", $content);

        return strip_tags($content);
    }

    protected function extractPdfText(string $path): string
    {
        $content = file_get_contents($path) ?: '';
        preg_match_all('/\(([^()]*)\)/', $content, $matches);

        $text = trim(implode(' ', $matches[1] ?? []));

        if ($text !== '') {
            return $text;
        }

        return $this->extractBinaryDocumentText($path);
    }

    protected function extractBinaryDocumentText(string $path): string
    {
        $content = file_get_contents($path) ?: '';
        $content = preg_replace('/[^\x20-\x7E\n\r\t]/', ' ', $content) ?? $content;

        return trim($content);
    }

    protected function extractSections(array $lines): array
    {
        $sections = [
            'general' => [],
            'summary' => [],
            'education' => [],
            'experience' => [],
            'projects' => [],
            'skills' => [],
        ];

        $currentSection = 'general';

        foreach ($lines as $line) {
            $heading = $this->detectSectionHeading($line);

            if ($heading !== null) {
                $currentSection = $heading;
                continue;
            }

            $sections[$currentSection][] = $line;
        }

        return $sections;
    }

    protected function detectSkills(string $text, Collection $masterSkills): array
    {
        $lowerText = Str::lower($text);
        $matchedSkills = [];

        foreach ($masterSkills as $skill) {
            $skillName = $skill->name;
            $escapedSkill = preg_quote(Str::lower($skillName), '/');

            if (preg_match('/\b'.$escapedSkill.'\b/', $lowerText)) {
                $matchedSkills[] = $skillName;
            }
        }

        sort($matchedSkills);

        return array_values(array_unique($matchedSkills));
    }

    protected function detectName(array $lines, string $fallbackName): string
    {
        foreach ($lines as $line) {
            if (Str::contains($line, '@') || preg_match('/\d{5,}/', $line)) {
                continue;
            }

            if (Str::wordCount($line) <= 5 && preg_match('/^[A-Za-z .]+$/', $line)) {
                return trim($line);
            }
        }

        return $fallbackName;
    }

    protected function detectEmail(string $text): ?string
    {
        preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $text, $matches);

        return $matches[0] ?? null;
    }

    protected function detectPhone(string $text): ?string
    {
        preg_match('/(?:\+91[- ]?)?[6-9]\d{9}/', $text, $matches);

        return $matches[0] ?? null;
    }

    protected function sectionOrKeywordText(array $sectionLines, array $allLines, array $keywords): string
    {
        if (! empty($sectionLines)) {
            return $this->arrayToText($sectionLines);
        }

        $filtered = array_filter($allLines, function (string $line) use ($keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains(Str::lower($line), Str::lower($keyword))) {
                    return true;
                }
            }

            return false;
        });

        return $this->arrayToText(array_slice(array_values($filtered), 0, 4));
    }
}
