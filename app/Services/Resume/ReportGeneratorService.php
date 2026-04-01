<?php

namespace App\Services\Resume;

use App\Models\Resume;

class ReportGeneratorService extends BaseResumeService
{
    public function generate(Resume $resume, array $parsedData, array $skillMatch, array $scores): array
    {
        $strengths = [];
        $improvements = [];

        if ($scores['contact_score'] >= 8) {
            $strengths[] = 'Contact details are present and easy to identify.';
        } else {
            $improvements[] = 'Add complete contact details including name, email, and phone number.';
        }

        if ($parsedData['education_found']) {
            $strengths[] = 'Education details are available in the resume.';
        } else {
            $improvements[] = 'Add a dedicated education section with degree, college, and marks.';
        }

        if ($parsedData['experience_found']) {
            $strengths[] = 'Experience or internship details improve the profile credibility.';
        } else {
            $improvements[] = 'Add internship, freelance, or practical experience details.';
        }

        if ($parsedData['projects_found']) {
            $strengths[] = 'Project details show practical implementation ability.';
        } else {
            $improvements[] = 'Add at least 2 clear academic or personal projects with tools used.';
        }

        if (! empty($skillMatch['matched_skills'])) {
            $strengths[] = 'Matched skills for '.$resume->jobRole?->title.' include '.implode(', ', $skillMatch['matched_skills']).'.';
        }

        if (! empty($skillMatch['missing_skills'])) {
            $improvements[] = 'Missing role skills: '.implode(', ', $skillMatch['missing_skills']).'.';
        }

        if ($scores['formatting_score'] < 8) {
            $improvements[] = 'Improve formatting by using separate sections and adding more complete details.';
        }

        $summary = 'The resume received '.$scores['total_score'].'/100 with a job-role match of '.$scores['job_match_percentage'].'%.';

        if ($resume->jobRole) {
            $summary .= ' Target job role: '.$resume->jobRole->title.'.';
        }

        return [
            'summary' => $summary,
            'strengths' => implode("\n", $strengths),
            'missing_skills' => implode(', ', $skillMatch['missing_skills']),
            'improvements' => implode("\n", $improvements),
            'report_json' => [
                'strengths' => $strengths,
                'missing_skills' => $skillMatch['missing_skills'],
                'improvements' => $improvements,
                'matched_skills' => $skillMatch['matched_skills'],
                'extra_skills' => $skillMatch['extra_skills'],
            ],
        ];
    }
}
