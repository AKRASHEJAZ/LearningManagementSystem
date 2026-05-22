<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminCertificateManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_revoke_and_reactivate_certificate(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->create();
        $course = Course::factory()->published()->create(['created_by' => $admin->id]);

        $certificate = Certificate::query()->create([
            'uuid' => (string) Str::uuid(),
            'certificate_number' => 'C-TEST-ABC',
            'user_id' => $student->id,
            'course_id' => $course->id,
            'issued_at' => now(),
            'status' => 'active',
        ]);

        $this
            ->actingAs($admin)
            ->post(route('admin.certificates.revoke', $certificate, absolute: false), ['reason' => 'Test'])
            ->assertRedirect();

        $this->assertSame('revoked', $certificate->refresh()->status);

        $this
            ->get(route('certificates.verify.show', $certificate, absolute: false))
            ->assertOk()
            ->assertSee('Revoked');

        $this
            ->actingAs($admin)
            ->post(route('admin.certificates.reactivate', $certificate, absolute: false))
            ->assertRedirect();

        $this->assertSame('active', $certificate->refresh()->status);
    }
}
