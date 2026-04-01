<?php

namespace App\Services\Resume;

use Illuminate\Support\Str;

class ResumeScorerService extends BaseResumeService
{
    public function score(array $parsedData, array $skillMatch): array
    {
        $contactScore = 0;
        $contactScore += $parsedData['candidate_name'] ? 4 : 0;
        $contactScore += $parsedData['candidate_email'] ? 3 : 0;
        $contactScore += $parsedData['candidate_phone'] ? 3 : 0;

        $educationScore = 0;
        if ($parsedData['education_found']) {
            $educationScore += 10;
            $educationScore += Str::length($parsedData['education_text']) > 40 ? 5 : 2;
        }

        $skillsCount = count($parsedData['detected_skills']);
        $skillsScore = min(25, ($skillsCount * 4) + ($parsedData['skills_text'] !== '' ? 5 : 0));

        $experienceScore = 0;
        if ($parsedData['experience_found']) {
            $experienceScore += 10;
            $experienceScore += Str::length($parsedData['experience_text']) > 60 ? 5 : 2;
            $experienceScore += preg_match('/\b\d+\+?\s*(years|months)\b/i', $parsedData['experience_text']) ? 5 : 0;
        }

        $projectsScore = 0;
        if ($parsedData['projects_found']) {
            $projectsScore += 5;
            $projectsScore += Str::length($parsedData['projects_text']) > 60 ? 5 : 2;
        }

        $availableSections = count(array_filter([
            $parsedData['education_found'],
            $parsedData['experience_found'],
            $parsedData['projects_found'],
            $parsedData['skills_text'] !== '',
        ]));

        $formattingScore = min(
            10,
            ($availableSections * 2) + (Str::length($parsedData['full_text']) > 500 ? 2 : 0)
        );

        $jobMatchScore = $skillMatch['job_match_score'];

        $totalScore = min(
            100,
            $contactScore +
            min(15, $educationScore) +
            $skillsScore +
            min(20, $experienceScore) +
            min(10, $projectsScore) +
            $formattingScore +
            $jobMatchScore
        );

        return [
            'contact_score' => min(10, $contactScore),
            'education_score' => min(15, $educationScore),
            'skills_score' => min(25, $skillsScore),
            'experience_score' => min(20, $experienceScore),
            'projects_score' => min(10, $projectsScore),
            'formatting_score' => min(10, $formattingScore),
            'job_match_score' => min(10, $jobMatchScore),
            'total_score' => $totalScore,
            'job_match_percentage' => $skillMatch['match_percentage'],
        ];
    }
}
