<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_name',
        'category',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query;
    }

    public function resumeSkillMaps(): HasMany
    {
        return $this->hasMany(ResumeSkillMap::class);
    }

    public function resumes(): BelongsToMany
    {
        return $this->belongsToMany(Resume::class, 'resume_skill_map')
            ->withPivot('matched_type')
            ->withTimestamps();
    }
}
