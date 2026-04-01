<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtractedResumeData extends Model
{
    protected $table = 'extracted_resume_data';

    protected $fillable = [
        'resume_id',
        'full_name',
        'email',
        'phone',
        'address',
        'education',
        'experience',
        'projects',
        'certifications',
        'extracted_skills',
        'summary',
        'raw_text',
    ];

    protected function casts(): array
    {
        return [
            'extracted_skills' => 'array',
        ];
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
