<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MyCertificatesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_my_certificates_page(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        Certificate::query()->create([
            'uuid' => (string) Str::uuid(),
            'certificate_number' => 'C-TEST-XYZ',
            'user_id' => $student->id,
            'course_id' => $course->id,
            'issued_at' => now(),
            'status' => 'active',
        ]);

        $this
            ->actingAs($student)
            ->get(route('certificates.mine', absolute: false))
            ->assertOk()
            ->assertSee('My certificates')
            ->assertSee($course->title);
    }
}
