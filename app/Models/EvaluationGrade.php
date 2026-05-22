<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class EvaluationGrade extends Model
{
    protected $fillable = [
        'evaluation_id',
        'course_id',
        'student_user_id',
        'graded_by_user_id',
        'score',
        'status',
        'feedback',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'graded_at' => 'datetime',
            'score' => 'integer',
        ];
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by_user_id');
    }
}
