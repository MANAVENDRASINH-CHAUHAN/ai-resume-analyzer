<?php

namespace App\Services;

class ResumeScorerService
{
    public function calculate(array $parsedData, array $matchedSkills): array
    {
        $contactScore = $this->calculateContactScore($parsedData);
        $educationScore = $this->calculateSectionScore($parsedData['education'] ?? '', 15);
        $skillsScore = $this->calculateSkillsScore($matchedSkills);
        $experienceScore = $this->calculateSectionScore($parsedData['experience'] ?? '', 20);
        $projectsScore = $this->calculateSectionScore($parsedData['projects'] ?? '', 10);
        $formattingScore = $this->calculateFormattingScore($parsedData);
        $jobMatchScore = $this->calculateJobMatchScore($matchedSkills);

        $totalScore = $contactScore
            + $educationScore
            + $skillsScore
            + $experienceScore
            + $projectsScore
            + $formattingScore
            + $jobMatchScore;

        return [
            'contact_score' => $contactScore,
            'education_score' => $educationScore,
            'skills_score' => $skillsScore,
            'experience_score' => $experienceScore,
            'projects_score' => $projectsScore,
            'formatting_score' => $formattingScore,
            'job_match_score' => $jobMatchScore,
            'total_score' => min(100, $totalScore),
            'feedback' => $this->buildFeedback($matchedSkills),
            'strengths' => $this->buildStrengths($parsedData, $matchedSkills),
            'improvements' => $this->buildImprovements($parsedData, $matchedSkills),
        ];
    }

    protected function calculateContactScore(array $parsedData): int
    {
        $score = 0;

        if (! empty($parsedData['full_name'])) {
            $score += 2;
        }

        if (! empty($parsedData['email'])) {
            $score += 4;
        }

        if (! empty($parsedData['phone'])) {
            $score += 3;
        }

        if (! empty($parsedData['address'])) {
            $score += 1;
        }

        return min(10, $score);
    }

    protected function calculateSectionScore(string $text, int $maxScore): int
    {
        $length = mb_strlen(trim($text));

        if ($length >= 150) {
            return $maxScore;
        }

        if ($length >= 60) {
            return (int) round($maxScore * 0.7);
        }

        if ($length >= 20) {
            return (int) round($maxScore * 0.4);
        }

        return 0;
    }

    protected function calculateSkillsScore(array $matchedSkills): int
    {
        $requiredSkills = $matchedSkills['job_role_required_skills'] ?? [];
        $detectedSkills = $matchedSkills['detected_skills'] ?? [];
        $matched = $matchedSkills['matched_skills'] ?? [];

        if (count($requiredSkills) > 0) {
            $matchContribution = (int) round((count($matched) / count($requiredSkills)) * 18);
            $detectionContribution = min(7, count($detectedSkills) * 2);

            return min(25, $matchContribution + $detectionContribution);
        }

        if (count($detectedSkills) === 0) {
            return 0;
        }

        return min(25, 5 + (count($detectedSkills) * 4));
    }

    protected function calculateFormattingScore(array $parsedData): int
    {
        $score = 0;
        $sectionCount = 0;

        foreach (['summary', 'education', 'experience', 'projects', 'certifications', 'skills_section'] as $section) {
            if (! empty(trim((string) ($parsedData[$section] ?? '')))) {
                $sectionCount++;
            }
        }

        if (mb_strlen($parsedData['raw_text'] ?? '') >= 200) {
            $score += 4;
        }

        $score += min(6, $sectionCount);

        return min(10, $score);
    }

    protected function calculateJobMatchScore(array $matchedSkills): int
    {
        $requiredSkills = $matchedSkills['job_role_required_skills'] ?? [];
        $matched = $matchedSkills['matched_skills'] ?? [];

        if (count($requiredSkills) === 0) {
            return count($matchedSkills['detected_skills'] ?? []) > 0 ? 5 : 0;
        }

        return (int) round((count($matched) / count($requiredSkills)) * 10);
    }

    protected function buildFeedback(array $matchedSkills): string
    {
        return 'Detected ' . count($matchedSkills['detected_skills'] ?? [])
            . ' skill(s). Matched ' . count($matchedSkills['matched_skills'] ?? [])
            . ' required skill(s) and missed ' . count($matchedSkills['missing_skills'] ?? [])
            . ' required skill(s).';
    }

    protected function buildStrengths(array $parsedData, array $matchedSkills): string
    {
        $strengths = [];

        if (! empty($parsedData['email']) && ! empty($parsedData['phone'])) {
            $strengths[] = 'Resume contains important contact details.';
        }

        if (! empty($parsedData['experience'])) {
            $strengths[] = 'Experience section is available.';
        }

        if (! empty($matchedSkills['matched_skills'])) {
            $strengths[] = 'Matched skills: ' . implode(', ', $matchedSkills['matched_skills']) . '.';
        }

        return implode("\n", $strengths);
    }

    protected function buildImprovements(array $parsedData, array $matchedSkills): string
    {
        $improvements = [];

        if (empty($parsedData['education'])) {
            $improvements[] = 'Add a clear education section.';
        }

        if (empty($parsedData['projects'])) {
            $improvements[] = 'Include project details to improve resume quality.';
        }

        if (! empty($matchedSkills['missing_skills'])) {
            $improvements[] = 'Missing skills: ' . implode(', ', $matchedSkills['missing_skills']) . '.';
        }

        if (empty($parsedData['summary'])) {
            $improvements[] = 'Add a short professional summary.';
        }

        return implode("\n", $improvements);
    }
}
