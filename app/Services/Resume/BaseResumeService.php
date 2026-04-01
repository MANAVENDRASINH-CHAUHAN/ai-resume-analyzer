<?php

namespace App\Services\Resume;

abstract class BaseResumeService
{
    protected array $config;

    protected array $sectionKeywords;

    public function __construct()
    {
        $this->config = config('resume_analyzer');
        $this->sectionKeywords = $this->config['section_keywords'];
    }

    protected function normaliseText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace("/\r\n|\r/", "\n", $text) ?? $text;
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
        $text = preg_replace("/\n{2,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }

    protected function prepareLines(string $text): array
    {
        $lines = preg_split('/\n+/', $text) ?: [];

        return array_values(array_filter(array_map('trim', $lines)));
    }

    protected function detectSectionHeading(string $line): ?string
    {
        $cleanLine = strtolower(trim($line, " :-\t"));

        foreach ($this->sectionKeywords as $section => $keywords) {
            foreach ($keywords as $keyword) {
                if ($cleanLine === strtolower($keyword)) {
                    return $section;
                }
            }
        }

        return null;
    }

    protected function arrayToText(array $items): string
    {
        return trim(implode("\n", array_filter($items)));
    }
}
