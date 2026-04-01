<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Resume extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'job_role_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'upload_status',
        'analysis_status',
        'progress_percent',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'progress_percent' => 'integer',
            'uploaded_at' => 'datetime',
        ];
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('analysis_status', 'completed');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $size = (int) $this->file_size;

        if ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' MB';
        }

        if ($size >= 1024) {
            return number_format($size / 1024, 2) . ' KB';
        }

        return $size . ' Bytes';
    }

    public function getUploadStatusBadgeClassAttribute(): string
    {
        return match ($this->upload_status) {
            'uploaded' => 'text-bg-primary',
            'processing' => 'text-bg-warning',
            'analyzed' => 'text-bg-success',
            'failed' => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    }

    public function getAnalysisStatusBadgeClassAttribute(): string
    {
        return match ($this->analysis_status) {
            'pending' => 'text-bg-secondary',
            'in_progress' => 'text-bg-warning',
            'processing' => 'text-bg-warning',
            'completed' => 'text-bg-success',
            'analyzed' => 'text-bg-success',
            'failed', 'error' => 'text-bg-danger',
            default => 'text-bg-light border text-dark',
        };
    }

    public function getDownloadUrlAttribute(): ?string
    {
        if (! $this->file_path || ! Storage::disk('public')->exists($this->file_path)) {
            return null;
        }

        return route('user.resumes.download', $this);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jobRole(): BelongsTo
    {
        return $this->belongsTo(JobRole::class);
    }

    public function extractedResumeData(): HasOne
    {
        return $this->hasOne(ExtractedResumeData::class);
    }

    public function resumeScore(): HasOne
    {
        return $this->hasOne(ResumeScore::class);
    }

    public function analysisReport(): HasOne
    {
        return $this->hasOne(AnalysisReport::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function resumeSkillMaps(): HasMany
    {
        return $this->hasMany(ResumeSkillMap::class);
    }

    public function resumeSkillMap(): HasMany
    {
        return $this->resumeSkillMaps();
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'resume_skill_map')
            ->withPivot('matched_type')
            ->withTimestamps();
    }
}
