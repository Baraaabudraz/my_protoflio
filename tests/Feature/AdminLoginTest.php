<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    public function test_admin_can_log_in_with_the_configured_password(): void
    {
        config(['app.admin_password' => 'correct-horse-battery']);

        $this->post(route('admin.login.post'), ['password' => 'correct-horse-battery'])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('admin_logged_in', true);
    }

    public function test_wrong_password_is_rejected(): void
    {
        config(['app.admin_password' => 'correct-horse-battery']);

        $this->from(route('admin.login'))
            ->post(route('admin.login.post'), ['password' => 'admin123'])
            ->assertRedirect(route('admin.login'))
            ->assertSessionMissing('admin_logged_in')
            ->assertSessionHas('error');
    }

    public function test_login_is_disabled_when_no_password_is_configured(): void
    {
        config(['app.admin_password' => null]);

        $this->from(route('admin.login'))
            ->post(route('admin.login.post'), ['password' => ''])
            ->assertRedirect(route('admin.login'))
            ->assertSessionMissing('admin_logged_in');

        $this->from(route('admin.login'))
            ->post(route('admin.login.post'), ['password' => 'admin123'])
            ->assertSessionMissing('admin_logged_in');
    }
}
