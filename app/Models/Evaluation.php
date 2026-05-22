<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'max_score',
        'is_required',
        'position',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'max_score' => 'integer',
            'position' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(EvaluationGrade::class);
    }
}
