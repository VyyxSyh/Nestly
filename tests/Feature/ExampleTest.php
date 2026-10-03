<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->actingAs(User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]));

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_logout_invalidates_the_session(): void
    {
        $this->actingAs(User::create([
            'name' => 'Test User',
            'email' => 'logout@example.com',
            'password' => 'password',
        ]));

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
