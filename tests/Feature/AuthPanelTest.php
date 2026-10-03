<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AuthPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_is_signed_in(): void
    {
        Livewire::test('auth-panel')
            ->set('name', 'Nestly User')
            ->set('email', 'nestly@example.com')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'password123')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertTrue(Hash::check('password123', User::where('email', 'nestly@example.com')->firstOrFail()->password));
    }

    public function test_user_can_log_in_with_valid_credentials(): void
    {
        $user = User::create([
            'name' => 'Nestly User',
            'email' => 'nestly@example.com',
            'password' => 'password123',
        ]);

        Livewire::test('auth-panel')
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_requires_password_confirmation_and_unique_email(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'nestly@example.com',
            'password' => 'password123',
        ]);

        Livewire::test('auth-panel')
            ->set('name', 'Another User')
            ->set('email', 'nestly@example.com')
            ->set('password', 'password123')
            ->set('passwordConfirmation', 'different123')
            ->call('register')
            ->assertHasErrors(['email' => 'unique', 'password' => 'confirmed']);
    }
}
