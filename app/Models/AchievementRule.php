<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AchievementRule extends Model
{
    protected $fillable = [
        'achievement_id',
        'type',
        'min_course_completions',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'min_course_completions' => 'integer',
        ];
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(AchievementRuleCourse::class);
    }
}
