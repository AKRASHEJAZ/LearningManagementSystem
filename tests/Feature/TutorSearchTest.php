<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseTutor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_users_on_tutor_page_and_add_by_user_id(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['name' => 'Alice Tutor']);
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        $this
            ->actingAs($admin)
            ->get(route('admin.courses.tutors', $course, absolute: false).'?q=Alice')
            ->assertOk()
            ->assertSee('Alice Tutor');

        $this
            ->actingAs($admin)
            ->post(route('admin.courses.tutors.store', $course, absolute: false), [
                'user_id' => $target->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('course_tutors', [
            'course_id' => $course->id,
            'user_id' => $target->id,
            'status' => 'active',
        ]);
    }
}
