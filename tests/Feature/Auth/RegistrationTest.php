<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that registration is disabled (FR-05)
     * Only admin can create user accounts via Karyawan management
     */
    public function test_registration_screen_is_disabled(): void
    {
        $response = $this->get('/register');

        // Registration route should return 404 (disabled)
        $response->assertStatus(404);
    }

    /**
     * Test that registration POST is disabled (FR-05)
     */
    public function test_new_users_cannot_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Registration should be disabled, expect 404
        $response->assertStatus(404);
        $this->assertGuest();
    }
}
