<?php

namespace App\Services;

class ResumeParserService
{
    public function parse(string $rawText, ?string $fallbackName = null): array
    {
        $lines = $this->getCleanLines($rawText);
        $sections = $this->extractSections($lines);

        return [
            'full_name' => $this->detectFullName($lines, $fallbackName),
            'email' => $this->extractEmail($rawText),
            'phone' => $this->extractPhone($rawText),
            'address' => $this->detectAddress($lines),
            'education' => $sections['education'] ?? '',
            'experience' => $sections['experience'] ?? '',
            'projects' => $sections['projects'] ?? '',
            'certifications' => $sections['certifications'] ?? '',
            'skills_section' => $sections['skills'] ?? '',
            'summary' => $sections['summary'] ?: mb_substr($rawText, 0, 600),
            'raw_text' => $rawText,
        ];
    }

    protected function getCleanLines(string $rawText): array
    {
        $lines = preg_split("/\n+/", $rawText) ?: [];

        return array_values(array_filter(array_map(function (string $line): string {
            return trim($line);
        }, $lines)));
    }

    protected function detectFullName(array $lines, ?string $fallbackName = null): ?string
    {
        $invalidWords = ['resume', 'curriculum vitae', 'education', 'experience', 'projects', 'skills', 'certifications'];

        foreach (array_slice($lines, 0, 6) as $line) {
            $lowerLine = strtolower($line);

            if (filter_var($line, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            if (preg_match('/\d{6,}/', $line)) {
                continue;
            }

            if (collect($invalidWords)->contains(fn (string $word) => str_contains($lowerLine, $word))) {
                continue;
            }

            if (preg_match('/^[A-Za-z][A-Za-z\s\.\-]{2,60}$/', $line)) {
                return trim($line);
            }
        }

        return $fallbackName ?: null;
    }

    protected function extractEmail(string $rawText): ?string
    {
        preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $rawText, $matches);

        return $matches[0] ?? null;
    }

    protected function extractPhone(string $rawText): ?string
    {
        preg_match('/(\+?\d[\d\-\s\(\)]{8,}\d)/', $rawText, $matches);

        return isset($matches[1]) ? trim($matches[1]) : null;
    }

    protected function detectAddress(array $lines): ?string
    {
        $keywords = ['address', 'street', 'road', 'lane', 'avenue', 'city', 'state', 'zip', 'pincode', 'india', 'usa'];

        foreach ($lines as $line) {
            $lowerLine = strtolower($line);

            foreach ($keywords as $keyword) {
                if (str_contains($lowerLine, $keyword)) {
                    return $line;
                }
            }
        }

        return null;
    }

    protected function extractSections(array $lines): array
    {
        $sectionKeywords = [
            'summary' => ['summary', 'professional summary', 'career objective', 'objective', 'profile'],
            'skills' => ['skills', 'technical skills', 'key skills'],
            'education' => ['education', 'academics', 'academic background'],
            'experience' => ['experience', 'work experience', 'professional experience', 'employment history'],
            'projects' => ['projects', 'academic projects', 'project experience'],
            'certifications' => ['certifications', 'certification', 'certificates'],
        ];

        $sections = [
            'summary' => [],
            'skills' => [],
            'education' => [],
            'experience' => [],
            'projects' => [],
            'certifications' => [],
        ];

        $currentSection = null;

        foreach ($lines as $line) {
            $detectedSection = $this->detectSectionHeading($line, $sectionKeywords);

            if ($detectedSection !== null) {
                $currentSection = $detectedSection;
                continue;
            }

            if ($currentSection !== null) {
                $sections[$currentSection][] = $line;
            }
        }

        return array_map(function (array $sectionLines): string {
            return trim(implode("\n", $sectionLines));
        }, $sections);
    }

    protected function detectSectionHeading(string $line, array $sectionKeywords): ?string
    {
        $normalizedLine = strtolower(trim($line, " :-\t"));

        foreach ($sectionKeywords as $sectionName => $keywords) {
            foreach ($keywords as $keyword) {
                if ($normalizedLine === $keyword || $normalizedLine === $keyword . ':') {
                    return $sectionName;
                }

                if (mb_strlen($normalizedLine) <= 35 && str_contains($normalizedLine, $keyword)) {
                    return $sectionName;
                }
            }
        }

        return null;
    }
}
