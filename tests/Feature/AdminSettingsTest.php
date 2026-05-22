<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_settings_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this
            ->actingAs($admin)
            ->get(route('admin.settings.edit', absolute: false))
            ->assertOk();
    }

    public function test_admin_can_update_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $this
            ->actingAs($admin)
            ->post(route('admin.settings.update', absolute: false), [
                'institute_name' => 'My College',
                'primary_color' => '#112233',
            ])
            ->assertRedirect(route('admin.settings.edit', absolute: false));

        $this->assertSame('My College', Setting::query()->where('key', 'institute.name')->value('value'));
        $this->assertSame('#112233', Setting::query()->where('key', 'brand.primary_color')->value('value'));
    }

    public function test_non_admin_cannot_access_settings(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get(route('admin.settings.edit', absolute: false))
            ->assertForbidden();
    }
}
