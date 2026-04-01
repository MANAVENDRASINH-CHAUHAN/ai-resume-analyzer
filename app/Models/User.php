<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'profile_image',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    public function scopeCandidates(Builder $query): Builder
    {
        return $query->where('role', 'candidate');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return $this->role === 'admin' ? 'text-bg-dark' : 'text-bg-primary';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status === 'active' ? 'text-bg-success' : 'text-bg-secondary';
    }

    public function resumes(): HasMany
    {
        return $this->hasMany(Resume::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function generatedReports(): HasMany
    {
        return $this->hasMany(AnalysisReport::class, 'generated_by');
    }
}
