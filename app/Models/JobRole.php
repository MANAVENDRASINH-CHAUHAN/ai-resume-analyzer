<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'required_skills',
        'preferred_experience',
        'min_score',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'required_skills' => 'array',
            'min_score' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function getRequiredSkillsListAttribute(): array
    {
        if (is_array($this->required_skills)) {
            return $this->required_skills;
        }

        if (is_string($this->required_skills) && trim($this->required_skills) !== '') {
            return array_filter(array_map('trim', explode(',', $this->required_skills)));
        }

        return [];
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status === 'active' ? 'text-bg-success' : 'text-bg-secondary';
    }

    public function resumes(): HasMany
    {
        return $this->hasMany(Resume::class);
    }
}
