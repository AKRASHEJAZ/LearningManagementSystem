<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCoursesTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_user_can_browse_published_courses_only(): void
    {
        $user = User::factory()->create();

        $published = Course::factory()->published()->create(['created_by' => $user->id]);
        $draft = Course::factory()->draft()->create(['created_by' => $user->id]);

        $response = $this
            ->actingAs($user)
            ->get(route('courses.index', absolute: false));

        $response->assertOk();
        $response->assertSee($published->title);
        $response->assertDontSee($draft->title);

        $this
            ->actingAs($user)
            ->get(route('courses.show', $published->slug, absolute: false))
            ->assertOk();

        $this
            ->actingAs($user)
            ->get(route('courses.show', $draft->slug, absolute: false))
            ->assertNotFound();
    }
}
