<?php

namespace App\Services;

use App\Models\JobRole;
use App\Models\Skill;
use Illuminate\Support\Collection;

class SkillMatcherService
{
    protected array $skillAliases = [
        'sql' => ['mysql', 'postgresql', 'sqlite', 'mssql', 'sol'],
        'javascript' => ['js'],
        'api' => ['rest api', 'restful api', 'web api'],
        'machine learning' => ['ml'],
        'data analysis' => ['analytics', 'data analytics'],
    ];

    public function __construct(protected Skill $skillModel)
    {
    }

    public function match(string $resumeText, ?JobRole $jobRole = null): array
    {
        $skills = $this->skillModel->newQuery()->orderBy('skill_name')->get();
        $normalizedResumeText = $this->normalizeSearchText($resumeText);
        $fallbackSkills = $this->extractFallbackSkills($resumeText);

        $detectedSkills = [];
        $detectedSkillRows = [];

        foreach ($skills as $skill) {
            if ($this->containsSkill($normalizedResumeText, $skill->skill_name)) {
                $detectedSkills[] = $skill->skill_name;
                $detectedSkillRows[$this->normalizeValue($skill->skill_name)] = [
                    'skill_id' => $skill->id,
                    'skill_name' => $skill->skill_name,
                ];
            }
        }

        $detectedSkills = array_values(array_unique(array_merge($detectedSkills, $fallbackSkills)));

        $requiredSkills = $this->normalizeRequiredSkills($jobRole?->required_skills_list ?? []);

        $matchedSkills = [];
        $missingSkills = [];
        $extraSkills = [];

        foreach ($requiredSkills as $requiredSkill) {
            if ($this->containsSkill($normalizedResumeText, $requiredSkill['name'])) {
                $matchedSkills[] = $requiredSkill['name'];
            } else {
                $missingSkills[] = $requiredSkill['name'];
            }
        }

        foreach ($detectedSkills as $detectedSkill) {
            $normalizedSkill = $this->normalizeValue($detectedSkill);

            if (collect($requiredSkills)->contains(fn (array $item) => $item['normalized'] === $normalizedSkill)) {
                continue;
            }

            $extraSkills[] = $detectedSkill;
        }

        $resumeSkillRows = $this->buildResumeSkillRows(
            $skills,
            $detectedSkillRows,
            $requiredSkills
        );

        $jobMatchPercentage = 0;

        if (count($requiredSkills) > 0) {
            $jobMatchPercentage = (int) round((count($matchedSkills) / count($requiredSkills)) * 100);
        }

        return [
            'detected_skills' => array_values(array_unique($detectedSkills)),
            'matched_skills' => array_values(array_unique($matchedSkills)),
            'missing_skills' => array_values(array_unique($missingSkills)),
            'extra_skills' => array_values(array_unique($extraSkills)),
            'job_role_required_skills' => array_map(fn (array $item) => $item['name'], $requiredSkills),
            'resume_skill_rows' => $resumeSkillRows,
            'job_match_percentage' => $jobMatchPercentage,
        ];
    }

    protected function containsSkill(string $resumeText, string $skillName): bool
    {
        foreach ($this->getComparableSkillTerms($skillName) as $term) {
            if (str_contains(' ' . $resumeText . ' ', ' ' . $term . ' ')) {
                return true;
            }
        }

        return false;
    }

    protected function normalizeRequiredSkills(array|string|null $requiredSkills): array
    {
        if (is_string($requiredSkills)) {
            $requiredSkills = array_filter(array_map('trim', explode(',', $requiredSkills)));
        }

        if (! is_array($requiredSkills)) {
            return [];
        }

        return array_values(array_map(function (string $skillName): array {
            return [
                'name' => $skillName,
                'normalized' => $this->normalizeValue($skillName),
            ];
        }, array_filter($requiredSkills)));
    }

    protected function buildResumeSkillRows(Collection $skills, array $detectedSkillRows, array $requiredSkills): array
    {
        $requiredLookup = collect($requiredSkills)->keyBy('normalized');
        $rows = [];

        foreach ($detectedSkillRows as $normalizedSkill => $skillRow) {
            $rows[] = [
                'skill_id' => $skillRow['skill_id'],
                'matched_type' => 'detected',
            ];

            $rows[] = [
                'skill_id' => $skillRow['skill_id'],
                'matched_type' => $requiredLookup->has($normalizedSkill) ? 'matched' : 'extra',
            ];
        }

        foreach ($skills as $skill) {
            $normalizedSkill = $this->normalizeValue($skill->skill_name);

            if ($requiredLookup->has($normalizedSkill) && ! isset($detectedSkillRows[$normalizedSkill])) {
                $rows[] = [
                    'skill_id' => $skill->id,
                    'matched_type' => 'missing',
                ];
            }
        }

        return $rows;
    }

    protected function extractFallbackSkills(string $resumeText): array
    {
        $lines = preg_split("/\r\n|\r|\n/", $resumeText) ?: [];
        $lines = array_values(array_filter(array_map('trim', $lines)));

        $startHeadings = [
            'skills',
            'technical skills',
            'key skills',
            'core skills',
            'core competencies',
            'competencies',
            'tools',
            'technologies',
            'languages',
        ];

        $stopHeadings = [
            'profile',
            'summary',
            'professional summary',
            'objective',
            'experience',
            'employment history',
            'work experience',
            'education',
            'projects',
            'certifications',
            'certification',
            'awards',
            'achievements',
        ];

        $collecting = false;
        $skills = [];

        foreach ($lines as $line) {
            $normalizedLine = $this->normalizeSearchText($line);

            if ($normalizedLine === '') {
                continue;
            }

            if (in_array($normalizedLine, $startHeadings, true)) {
                $collecting = true;
                continue;
            }

            if ($collecting && in_array($normalizedLine, $stopHeadings, true)) {
                $collecting = false;
                continue;
            }

            if (! $collecting) {
                continue;
            }

            foreach ($this->splitSkillLine($line) as $skillItem) {
                if (! $this->looksLikeSkill($skillItem)) {
                    continue;
                }

                $skills[] = $this->formatSkillLabel($skillItem);
            }
        }

        return array_values(array_unique(array_filter($skills)));
    }

    protected function splitSkillLine(string $line): array
    {
        $parts = preg_split('/[,|\/•]+/', $line) ?: [$line];

        return array_values(array_filter(array_map('trim', $parts)));
    }

    protected function looksLikeSkill(string $value): bool
    {
        $normalizedValue = $this->normalizeSearchText($value);

        if ($normalizedValue === '' || mb_strlen($normalizedValue) < 2 || mb_strlen($normalizedValue) > 40) {
            return false;
        }

        if (str_word_count($normalizedValue) > 3) {
            return false;
        }

        if (preg_match('/\d/', $value)) {
            return false;
        }

        $blockedPhrases = [
            'hardworking student',
            'employment history',
            'bachelor of communications',
            'high school diploma',
            'new york',
            'united states',
            'resume worded',
            'growthsi',
            'worked with sales',
            'customer requirement',
            'line',
            'and skills',
            'associate data scientist',
            'data scientist',
            'wealth management sales',
            'ny',
            'ma',
        ];

        if (in_array($normalizedValue, $blockedPhrases, true)) {
            return false;
        }

        $blockedWords = [
            'january', 'february', 'march', 'april', 'may', 'june',
            'july', 'august', 'september', 'october', 'november', 'december',
            'present', 'worked', 'customer', 'results', 'opportunities',
            'resources', 'boston', 'sales', 'identify',
        ];

        foreach (explode(' ', $normalizedValue) as $word) {
            if (in_array($word, $blockedWords, true)) {
                return false;
            }
        }

        $words = preg_split('/\s+/', trim($value)) ?: [];
        $titleLikeWords = 0;

        foreach ($words as $word) {
            $cleanWord = trim($word, " \t\n\r\0\x0B.,;:()[]{}");

            if ($cleanWord === '') {
                continue;
            }

            if (preg_match('/^(?:[A-Z][A-Za-z\+\#&\-]*|[A-Z]{2,}|C\+\+|SQL|MySQL)$/', $cleanWord)) {
                $titleLikeWords++;
            }
        }

        return count($words) > 0 && ($titleLikeWords / count($words)) >= 0.7;
    }

    protected function formatSkillLabel(string $value): string
    {
        $value = trim($value);

        if (preg_match('/^[A-Z0-9 \/\+\-]+$/', $value)) {
            $value = ucwords(strtolower($value));
            $value = str_replace(['Sql', 'Mysql', 'Sol'], ['SQL', 'MySQL', 'SQL'], $value);

            return $value;
        }

        return preg_replace('/\s+/', ' ', $value) ?? $value;
    }

    protected function normalizeValue(string $value): string
    {
        return $this->normalizeSearchText($value);
    }

    protected function getComparableSkillTerms(string $skillName): array
    {
        $normalizedSkill = $this->normalizeSearchText($skillName);
        $aliases = $this->skillAliases[$normalizedSkill] ?? [];

        return collect([$normalizedSkill, ...$aliases])
            ->map(fn (string $term) => $this->normalizeSearchText($term))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function normalizeSearchText(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }
}
