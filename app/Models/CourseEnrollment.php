<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'status',
        'result_status',
        'result_updated_at',
        'result_updated_by',
        'applied_at',
        'accepted_at',
        'rejected_at',
        'withdrawn_at',
        'completed_at',
        'updated_by',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'result_updated_at' => 'datetime',
            'applied_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'withdrawn_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
