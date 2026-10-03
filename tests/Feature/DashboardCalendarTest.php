<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_marks_deadlines_and_shows_selected_date_details(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $subject = Subject::create(['name' => 'Matematika', 'accent_color' => '#FF4D8D', 'user_id' => $user->id]);
        $task = Task::create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'title' => 'Latihan aljabar',
            'deadline' => '2026-10-14',
            'progress_mode' => 'manual',
            'progress' => 25,
        ]);
        Schedule::create([
            'user_id' => $user->id,
            'subject_id' => $subject->id,
            'day' => 'Rabu',
            'start_time' => '09:00',
            'end_time' => '10:30',
            'room' => 'A1',
        ]);

        Livewire::test('dashboard')
            ->assertSee('Oktober 2026')
            ->call('selectCalendarDate', '2026-10-14')
            ->assertSee('Latihan aljabar')
            ->assertSee('Matematika')
            ->assertSee('09:00–10:30');

        $this->assertSame('2026-10-14', $task->deadline->toDateString());
    }

    public function test_calendar_month_navigation_updates_year_and_resets_selection(): void
    {
        $this->actingAs(User::factory()->create());
        $this->travelTo(Carbon::parse('2026-10-02'));

        Livewire::test('dashboard')
            ->call('changeCalendarMonth', 1)
            ->call('changeCalendarMonth', 1)
            ->assertSee(Carbon::parse('2026-12-01')->translatedFormat('F Y'))
            ->call('selectCalendarDate', '2026-12-24')
            ->call('changeCalendarMonth', 1)
            ->assertSee(Carbon::parse('2027-01-01')->translatedFormat('F Y'))
            ->assertSet('selectedCalendarDate', null)
            ->call('changeCalendarMonth', -1)
            ->assertSee(Carbon::parse('2026-12-01')->translatedFormat('F Y'))
            ->assertSet('selectedCalendarDate', null);
    }

    public function test_calendar_rejects_invalid_selected_dates(): void
    {
        $this->actingAs(User::factory()->create());
        Livewire::test('dashboard')
            ->call('selectCalendarDate', '2026-02-31')
            ->assertStatus(404);
    }
}
