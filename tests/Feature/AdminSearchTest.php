<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_search_returns_results(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create(['name' => 'Alice Example', 'email' => 'alice@example.com']);
        $course = Course::factory()->published()->create(['created_by' => $admin->id, 'title' => 'Git Basics', 'slug' => 'git-basics']);

        Certificate::query()->create([
            'uuid' => (string) Str::uuid(),
            'certificate_number' => 'C-SEARCH-123',
            'user_id' => $student->id,
            'course_id' => $course->id,
            'issued_at' => now(),
            'status' => 'active',
        ]);

        Achievement::query()->create([
            'key' => 'git.guru',
            'name' => 'Git Guru',
            'description' => null,
            'icon' => 'crown',
            'points' => 200,
            'tier' => 'gold',
            'is_active' => true,
        ]);

        $this
            ->actingAs($admin)
            ->get(route('admin.search', ['q' => 'Git'], absolute: false))
            ->assertOk()
            ->assertSee('Courses')
            ->assertSee('Git Basics')
            ->assertSee('Achievements')
            ->assertSee('Git Guru');
    }
}

