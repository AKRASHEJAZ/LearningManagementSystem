<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'duration',
        'description',
        'completion_criteria',
        'max_participants',
        'started_at',
        'ended_at',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'max_participants' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function tutors(): HasMany
    {
        return $this->hasMany(CourseTutor::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function isStarted(): bool
    {
        return $this->started_at !== null;
    }

    public function isEnded(): bool
    {
        return $this->ended_at !== null;
    }

    public function applicationsOpen(): bool
    {
        return $this->isPublished() && ! $this->isStarted() && ! $this->isEnded();
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }
}
