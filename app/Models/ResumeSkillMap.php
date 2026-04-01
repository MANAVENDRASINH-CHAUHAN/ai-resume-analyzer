<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeSkillMap extends Model
{
    protected $table = 'resume_skill_map';

    protected $fillable = [
        'resume_id',
        'skill_id',
        'matched_type',
    ];

    public function scopeDetected(Builder $query): Builder
    {
        return $query->where('matched_type', 'detected');
    }

    public function scopeMatched(Builder $query): Builder
    {
        return $query->where('matched_type', 'matched');
    }

    public function scopeMissing(Builder $query): Builder
    {
        return $query->where('matched_type', 'missing');
    }

    public function scopeExtra(Builder $query): Builder
    {
        return $query->where('matched_type', 'extra');
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}
