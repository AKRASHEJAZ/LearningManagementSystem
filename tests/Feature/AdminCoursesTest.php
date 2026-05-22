<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCoursesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_course(): void
    {
        $admin = User::factory()->admin()->create();

        $this
            ->actingAs($admin)
            ->post(route('admin.courses.store', absolute: false), [
                'title' => 'Intro to Web',
                'slug' => '',
                'summary' => 'Basics',
                'duration' => '6 weeks',
                'description' => 'Hello',
                'completion_criteria' => 'Pass all required evaluations',
                'status' => 'draft',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('courses', [
            'title' => 'Intro to Web',
            'status' => 'draft',
            'created_by' => $admin->id,
        ]);
    }

    public function test_non_admin_cannot_access_course_admin(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get(route('admin.courses.index', absolute: false))
            ->assertForbidden();
    }
}
