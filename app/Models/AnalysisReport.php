<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisReport extends Model
{
    protected $fillable = [
        'resume_id',
        'report_title',
        'report_text',
        'report_file',
        'generated_by',
    ];

    public function getParagraphsAttribute(): array
    {
        return array_values(array_filter(array_map('trim', preg_split("/\n+/", (string) $this->report_text))));
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
