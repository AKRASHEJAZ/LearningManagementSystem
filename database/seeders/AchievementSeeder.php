<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'key' => 'account.approved',
                'name' => 'Verified Member',
                'description' => 'Your account was approved and you joined the community.',
                'icon' => 'shield',
                'tier' => 'bronze',
                'points' => 10,
            ],
            [
                'key' => 'course.completed.first',
                'name' => 'First Completion',
                'description' => 'Completed your first course.',
                'icon' => 'trophy',
                'tier' => 'bronze',
                'points' => 25,
            ],
            [
                'key' => 'course.completed.five',
                'name' => 'Committed Learner',
                'description' => 'Completed 5 courses.',
                'icon' => 'medal',
                'tier' => 'silver',
                'points' => 100,
            ],
            [
                'key' => 'course.completed.ten',
                'name' => 'Relentless',
                'description' => 'Completed 10 courses.',
                'icon' => 'crown',
                'tier' => 'gold',
                'points' => 250,
            ],
        ];

        foreach ($rows as $row) {
            Achievement::query()->updateOrCreate(
                ['key' => $row['key']],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'icon' => $row['icon'],
                    'tier' => $row['tier'],
                    'points' => $row['points'],
                    'is_active' => true,
                ]
            );
        }
    }
}

