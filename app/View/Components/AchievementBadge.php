<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AchievementBadge extends Component
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public string $tier = 'bronze',
        public string $icon = 'badge',
        public ?string $earnedAt = null,
        public bool $locked = false,
        public int $points = 0,
    ) {
    }

    public function render(): View
    {
        return view('components.achievement-badge');
    }
}

