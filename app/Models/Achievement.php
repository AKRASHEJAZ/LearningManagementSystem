<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Achievement extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'points',
        'tier',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function rule(): HasOne
    {
        return $this->hasOne(AchievementRule::class);
    }
}
