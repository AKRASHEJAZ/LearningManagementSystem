<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AchievementRuleCourse extends Model
{
    protected $fillable = [
        'achievement_rule_id',
        'course_id',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(AchievementRule::class, 'achievement_rule_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}

