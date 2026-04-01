<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeScore extends Model
{
    protected $fillable = [
        'resume_id',
        'contact_score',
        'education_score',
        'skills_score',
        'experience_score',
        'projects_score',
        'formatting_score',
        'job_match_score',
        'total_score',
        'feedback',
        'strengths',
        'improvements',
    ];

    protected function casts(): array
    {
        return [
            'contact_score' => 'integer',
            'education_score' => 'integer',
            'skills_score' => 'integer',
            'experience_score' => 'integer',
            'projects_score' => 'integer',
            'formatting_score' => 'integer',
            'job_match_score' => 'integer',
            'total_score' => 'integer',
        ];
    }

    public function getJobMatchPercentageAttribute(): int
    {
        return min(100, max(0, ((int) $this->job_match_score) * 10));
    }

    public function getRecommendationLabelAttribute(): string
    {
        return match (true) {
            $this->total_score >= 80 => 'Excellent',
            $this->total_score >= 60 => 'Good',
            $this->total_score >= 40 => 'Average',
            default => 'Needs Improvement',
        };
    }

    public function getRecommendationBadgeClassAttribute(): string
    {
        return match ($this->recommendation_label) {
            'Excellent' => 'text-bg-success',
            'Good' => 'text-bg-primary',
            'Average' => 'text-bg-warning',
            default => 'text-bg-danger',
        };
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
