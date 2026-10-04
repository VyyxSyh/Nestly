<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserPreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_preferences_are_saved_with_allowed_values(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test('nav')
            ->set('themeMode', 'light')
            ->set('language', 'en')
            ->set('themeColor', 'blue')
            ->set('titleFont', 'handwritting')
            ->set('descriptionFont', 'sans_serif');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'theme_mode' => 'light',
            'language' => 'en',
            'theme_color' => 'blue',
            'title_font' => 'handwritting',
            'description_font' => 'sans_serif',
        ]);
    }

    public function test_dashboard_saves_preset_and_custom_greetings(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test('dashboard')
            ->call('saveGreeting', 'Hii')
            ->assertSet('greeting', 'Hii')
            ->set('customGreeting', 'Good morning')
            ->call('saveCustomGreeting')
            ->assertSet('greeting', 'Good morning');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'greeting' => 'Good morning']);
    }

    public function test_account_profile_fields_and_password_are_editable(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test('nav')
            ->set('name', 'Full Name')
            ->set('nickname', 'Sunny')
            ->set('newPassword', 'NewPassword8')
            ->call('updatePassword');

        $user->refresh();
        $this->assertSame('Full Name', $user->name);
        $this->assertSame('Sunny', $user->nickname);
        $this->assertTrue(password_verify('NewPassword8', $user->password));
    }
}
