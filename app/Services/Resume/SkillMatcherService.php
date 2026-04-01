<?php

namespace App\Services\Resume;

use App\Models\JobRole;

class SkillMatcherService extends BaseResumeService
{
    public function match(array $detectedSkills, ?JobRole $jobRole): array
    {
        if ($jobRole === null) {
            return [
                'required_skills' => [],
                'matched_skills' => [],
                'missing_skills' => [],
                'extra_skills' => $detectedSkills,
                'match_percentage' => 0,
                'job_match_score' => 0,
            ];
        }

        $requiredSkills = $jobRole->skills()->pluck('name')->toArray();
        $requiredLookup = array_map('strtolower', $requiredSkills);
        $detectedLookup = array_map('strtolower', $detectedSkills);

        $matchedSkills = [];
        $missingSkills = [];

        foreach ($requiredSkills as $requiredSkill) {
            if (in_array(strtolower($requiredSkill), $detectedLookup, true)) {
                $matchedSkills[] = $requiredSkill;
            } else {
                $missingSkills[] = $requiredSkill;
            }
        }

        $extraSkills = array_values(array_filter($detectedSkills, function (string $skill) use ($requiredLookup) {
            return ! in_array(strtolower($skill), $requiredLookup, true);
        }));

        $totalRequired = count($requiredSkills);
        $matchPercentage = $totalRequired > 0
            ? (int) round((count($matchedSkills) / $totalRequired) * 100)
            : 0;

        return [
            'required_skills' => $requiredSkills,
            'matched_skills' => $matchedSkills,
            'missing_skills' => $missingSkills,
            'extra_skills' => $extraSkills,
            'match_percentage' => $matchPercentage,
            'job_match_score' => min(10, (int) round($matchPercentage / 10)),
        ];
    }
}
