<?php

use App\Models\Budget;
use App\Models\FinanceRecord;
use App\Models\Schedule;
use App\Models\Task;
use Carbon\Carbon;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $calendarMonth;

    #[Locked]
    public int $calendarYear;

    #[Locked]
    public ?string $selectedCalendarDate = null;

    public bool $editingGreeting = false;

    #[Validate('required|string|max:100')]
    public string $greeting = '';

    public string $customGreeting = '';

    public function mount(): void
    {
        $today = now();
        $this->calendarMonth = $today->month;
        $this->calendarYear = $today->year;
        $this->greeting = auth()->user()->greeting ?: 'Hello!';
    }

    #[On('profile-updated')]
    public function refreshProfile(): void {}

    public function changeCalendarMonth(int $direction): void
    {
        $month = Carbon::create($this->calendarYear, $this->calendarMonth, 1)
            ->addMonths($direction);

        $this->calendarMonth = $month->month;
        $this->calendarYear = $month->year;
        $this->selectedCalendarDate = null;
    }

    public function selectCalendarDate(string $date): void
    {
        try {
            $selectedDate = Carbon::createFromFormat('!Y-m-d', $date);
        } catch (Throwable) {
            abort(404);
        }

        abort_unless($selectedDate->format('Y-m-d') === $date, 404);

        $this->selectedCalendarDate = $date;
    }

    public function saveGreeting(string $greeting): void
    {
        $this->authorizeGreeting($greeting);
        $this->greeting = $greeting;
        $this->validateOnly('greeting');
        auth()->user()->update(['greeting' => $this->greeting]);
        $this->editingGreeting = false;
    }

    public function saveCustomGreeting(): void
    {
        $this->greeting = trim($this->customGreeting);
        $this->validateOnly('greeting');
        auth()->user()->update(['greeting' => $this->greeting]);
        $this->editingGreeting = false;
    }

    private function authorizeGreeting(string $greeting): void
    {
        abort_unless(in_array($greeting, ['Hello', 'Hii', "What's up?", 'Heyy', 'Allooww'], true), 422);
    }

    public function with(): array
    {
        $totalTasks = Task::where('user_id', auth()->id())->count();
        $completed = Task::where('user_id', auth()->id())->where('progress', '>=', 100)->count();
        $notStarted = Task::where('user_id', auth()->id())->where('progress', 0)->count();
        $inProgress = Task::where('user_id', auth()->id())->where('progress', '>', 0)->where('progress', '<', 100)->count();

        $nearestTasks = Task::where('user_id', auth()->id())->with('subject')
            ->where('progress', '<', 100)
            ->orderBy('deadline')
            ->orderBy('deadline_time')
            ->limit(10)
            ->get();

        // --- Jadwal hari ini ---
        $hariIni = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][now()->dayOfWeekIso - 1];
        $allToday = Schedule::where('user_id', auth()->id())->with('subject')->where('day', $hariIni)->orderBy('start_time')->get();

        // --- Keuangan bulan berjalan ---
        $monthQuery = fn () => FinanceRecord::where('user_id', auth()->id())->whereYear('date', now()->year)->whereMonth('date', now()->month);
        $totalIncome = (float) $monthQuery()->where('type', 'income')->sum('amount');
        $totalExpense = (float) $monthQuery()->where('type', 'expense')->sum('amount');
        $recentRecords = $monthQuery()->orderByDesc('date')->orderByDesc('id')->limit(5)->get();

        $budget = Budget::where('user_id', auth()->id())->where('month', now()->month)->where('year', now()->year)->first()
            ?? Budget::where('user_id', auth()->id())->orderByDesc('year')->orderByDesc('month')->first();
        $budgetAmount = (float) ($budget?->amount ?? 0);

        $calendarStart = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->startOfDay();
        $calendarEnd = $calendarStart->copy()->endOfMonth();
        $calendarGridStart = $calendarStart->copy()->startOfWeek(Carbon::MONDAY);
        $calendarGridEnd = $calendarEnd->copy()->endOfWeek(Carbon::SUNDAY);
        $calendarDays = collect();

        for ($day = $calendarGridStart->copy(); $day->lte($calendarGridEnd); $day->addDay()) {
            $calendarDays->push($day->copy());
        }

        $calendarTasks = Task::where('user_id', auth()->id())->with('subject')
            ->whereDate('deadline', '>=', $calendarGridStart->toDateString())
            ->whereDate('deadline', '<=', $calendarGridEnd->toDateString())
            ->orderBy('deadline')
            ->orderBy('deadline_time')
            ->get()
            ->groupBy(fn (Task $task) => $task->deadline->toDateString());

        $selectedDate = $this->selectedCalendarDate
            ? Carbon::createFromFormat('!Y-m-d', $this->selectedCalendarDate)
            : null;
        $selectedDaySchedules = $selectedDate
            ? Schedule::where('user_id', auth()->id())->with('subject')
                ->where('day', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][$selectedDate->dayOfWeekIso - 1])
                ->orderBy('start_time')
                ->get()
            : collect();

        return [
            'greeting' => $this->greeting,
            'nickname' => auth()->user()->nickname,
            'totalTasks' => $totalTasks,
            'completed' => $completed,
            'notStarted' => $notStarted,
            'inProgress' => $inProgress,
            'unfinished' => $totalTasks - $completed,
            'nearestTasks' => $nearestTasks,
            'todaySchedules' => $allToday->take(4),
            'scheduleExtra' => max(0, $allToday->count() - 4),
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netBalance' => $totalIncome - $totalExpense,
            'recentRecords' => $recentRecords,
            'budgetPercent' => $budgetAmount > 0 ? (int) round($totalExpense / $budgetAmount * 100) : null,
            'calendarDays' => $calendarDays,
            'calendarMonthLabel' => $calendarStart->translatedFormat('F Y'),
            'calendarTasks' => $calendarTasks,
            'selectedCalendarDate' => $selectedDate,
            'selectedDaySchedules' => $selectedDaySchedules,
            'selectedDateTasks' => $selectedDate ? $calendarTasks->get($selectedDate->toDateString(), collect()) : collect(),
        ];
    }
};
?>

@php
    $card = 'bg-surface border-2 border-border rounded-[14px] p-3 lg:p-4';
    $stats = [
        ['Total Task', $totalTasks, 'text-text'],
        ['Not Started', $notStarted, 'text-text-muted'],
        ['In Progress', $inProgress, 'text-warning'],
        ['Completed', $completed, 'text-success'],
    ];
    $budgetColor = match (true) {
        $budgetPercent === null => 'text-text-muted',
        $budgetPercent >= 100 => 'text-danger',
        $budgetPercent >= 80 => 'text-warning',
        default => 'text-success',
    };
@endphp

<div class="grid grid-cols-1 gap-2.5 text-text sm:grid-cols-6 sm:grid-rows-[auto_repeat(3,minmax(8rem,auto))]">

    {{-- 1. Jadwal (kolom 1-5, baris 1) --}}
    <section class="{{ $card }} sm:col-span-5 sm:row-start-1">
        <h2 class="text-sm lg:text-base font-bold mb-2">
            Jadwal Hari Ini
            <span class="font-normal text-text-muted text-xs lg:text-sm">· {{ now()->translatedFormat('l, d F') }}</span>
        </h2>

        @if ($todaySchedules->isEmpty())
            <p class="text-xs lg:text-sm text-text-muted">Tidak ada jadwal hari ini.</p>
        @else
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 pb-1 pr-1">
                @foreach ($todaySchedules as $schedule)
                    @php $accent = $schedule->subject?->accent_color ?? '#9CA3AF'; @endphp
                    <div class="flex items-center gap-2.5 rounded-[14px] border-2 bg-surface p-2.5 lg:p-3"
                         style="border-color: {{ $accent }}; box-shadow: 4px 4px 0px {{ $accent }};">
                        <div class="flex shrink-0 flex-col items-center leading-none">
                            <span class="text-xl lg:text-2xl font-bold">{{ substr($schedule->start_time, 0, 2) }}</span>
                            <span class="text-[10px] lg:text-xs text-text-muted">{{ substr($schedule->start_time, 3, 2) }}</span>
                            <span class="my-1 h-3 w-px" style="background: {{ $accent }}"></span>
                            <span class="text-[10px] lg:text-xs text-text-muted">{{ substr($schedule->end_time, 0, 5) }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="line-clamp-2 text-xs lg:text-sm font-bold leading-tight">{{ $schedule->subject?->name }}</div>
                            @if ($schedule->room)
                                <div class="truncate text-[10px] lg:text-xs text-text-muted">Ruang {{ $schedule->room }}</div>
                            @endif
                            @if ($schedule->lecturer)
                                <div class="truncate text-[10px] lg:text-xs text-text-muted">{{ $schedule->lecturer }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($scheduleExtra > 0)
                <p class="mt-1 text-xs text-text-muted">+{{ $scheduleExtra }} jadwal lainnya hari ini</p>
            @endif
        @endif
    </section>

    {{-- 2. Ringkasan singkat (kolom 6, baris 1) --}}
    <section class="{{ $card }} sm:col-start-6 sm:row-start-1 grid grid-cols-2 sm:grid-cols-1 gap-2 content-center">
        <div>
            <div class="text-[10px] lg:text-xs leading-tight text-text-muted">Tugas belum selesai</div>
            <div class="text-2xl lg:text-3xl font-bold">{{ $unfinished }}</div>
        </div>
        <div>
            <div class="text-[10px] lg:text-xs leading-tight text-text-muted">Budget terpakai</div>
            @if ($budgetPercent === null)
                <div class="text-sm font-semibold text-text-muted">Belum ada budget</div>
            @else
                <div class="text-2xl lg:text-3xl font-bold {{ $budgetColor }}">{{ $budgetPercent }}%</div>
            @endif
        </div>
    </section>

    {{-- 3. Tugas (kolom 1-2, baris 2-4) --}}
    <section class="{{ $card }} flex flex-col gap-3 sm:col-span-2 sm:col-start-1 sm:row-span-3 sm:row-start-2">
        <div class="flex justify-evenly rounded-xl bg-bg px-2 py-2 text-center">
            @foreach ($stats as [$label, $value, $color])
                <div>
                    <div class="text-[10px] lg:text-xs text-text-muted">{{ $label }}</div>
                    <div class="text-lg lg:text-2xl font-bold {{ $color }}">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        <div>
            <h2 class="text-sm lg:text-base font-bold mb-3">Deadline Terdekat</h2>
            @if ($nearestTasks->isEmpty())
                <p class="text-xs lg:text-sm text-text-muted">
                    {{ $totalTasks === 0 ? 'Belum ada tugas.' : 'Semua tugas sudah selesai.' }}
                </p>
            @else
                <div class="columns-1 gap-3 [&>*]:mb-3 [&>*]:break-inside-avoid">
                    @foreach ($nearestTasks as $task)
                        @php
                            $accent = $task->subject?->accent_color ?? '#9CA3AF';
                            $urgencyHex = match ($task->urgency_color) {
                                'red' => '#EF4444',
                                'orange' => '#F59E0B',
                                'yellow' => '#EAB308',
                                'gray' => '#9CA3AF',
                                default => '#22C55E',
                            };
                        @endphp
                        <div class="relative rounded-xl border-2 bg-surface px-3 pt-4 pb-2.5"
                             style="border-color: {{ $accent }}; box-shadow: 3px 3px 0px {{ $accent }};">
                            @if ($task->subject)
                                <span class="absolute -top-2.5 right-3 max-w-[75%] truncate rounded-full border-2 bg-surface px-2 py-0.5 text-[10px] font-semibold"
                                      style="border-color: {{ $accent }}; color: {{ $accent }};">
                                    {{ $task->subject->name }}
                                </span>
                            @endif
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="truncate text-xs lg:text-sm font-semibold">{{ $task->title }}</div>
                                    <div class="flex items-center gap-1.5 text-[10px] lg:text-xs text-text-muted">
                                        <span>{{ $task->deadline_formatted }}</span>
                                        <span class="font-semibold capitalize" style="color: {{ $urgencyHex }}">
                                            · {{ $task->priority }}
                                        </span>
                                    </div>
                                </div>
                                <span class="shrink-0 text-sm lg:text-base font-bold" style="color: {{ $accent }}">
                                    {{ $task->progress }}%
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- 4. Transaksi (kolom 3-4, baris 2-4) --}}
    <section class="{{ $card }} flex flex-col gap-3 sm:col-span-2 sm:col-start-3 sm:row-span-3 sm:row-start-2">
        <div class="space-y-1 rounded-xl bg-bg px-3 py-2 text-xs lg:text-sm">
            <div class="flex justify-between gap-2">
                <span class="text-text-muted">Pemasukan</span>
                <span class="font-semibold text-success">Rp{{ number_format($totalIncome, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between gap-2">
                <span class="text-text-muted">Pengeluaran</span>
                <span class="font-semibold text-danger">Rp{{ number_format($totalExpense, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between gap-2 border-t border-border pt-1">
                <span class="text-text-muted">Sisa Saldo</span>
                <span class="font-bold {{ $netBalance < 0 ? 'text-danger' : 'text-text' }}">Rp{{ number_format($netBalance, 0, ',', '.') }}</span>
            </div>
        </div>

        <div>
            <h2 class="text-sm lg:text-base font-bold mb-2">Transaksi {{ now()->translatedFormat('F') }}</h2>
            <div class="space-y-2">
                @forelse ($recentRecords as $record)
                    <div class="flex items-center justify-between gap-2 rounded-xl border border-border px-3 py-2">
                        <div class="min-w-0">
                            <div class="truncate text-xs lg:text-sm font-semibold">{{ $record->category }}</div>
                            <div class="truncate text-[10px] lg:text-xs text-text-muted">
                                {{ $record->date->translatedFormat('d M') }}@if ($record->note) · {{ $record->note }}@endif
                            </div>
                        </div>
                        <div class="shrink-0 text-xs lg:text-sm font-bold {{ $record->type === 'income' ? 'text-success' : 'text-danger' }}">
                            {{ $record->type === 'income' ? '+' : '−' }}Rp{{ number_format($record->amount, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <p class="text-xs lg:text-sm text-text-muted">Belum ada transaksi bulan ini.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Greeting + profil occupy the right-hand grid column. --}}
    <div class="flex flex-col gap-2.5 sm:col-span-2 sm:col-start-5 sm:row-span-3 sm:row-start-2">
        <section class="{{ $card }} shrink-0">
            <div class="flex items-center justify-between gap-2">
                <h1 class="text-lg font-bold lg:text-xl">{{ rtrim($greeting, '!.?') }}{{ $nickname ? ', '.$nickname : '' }}!</h1>
                <button type="button" wire:click="$set('editingGreeting', {{ $editingGreeting ? 'false' : 'true' }})" aria-label="Edit greeting" title="Edit greeting" class="flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-full text-text-muted transition hover:bg-primary/10 hover:text-primary focus-visible:outline-2 focus-visible:outline-primary">
                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                </button>
            </div>
            @if ($editingGreeting)
                <div class="mt-3 grid w-full grid-cols-2 gap-2 text-left">
                    @foreach (["Hello", 'Hii', "What's up?", 'Heyy', 'Allooww'] as $greetingOption)
                        <button type="button" wire:click="saveGreeting(@js($greetingOption))" class="rounded-lg border border-border px-2 py-1.5 text-sm transition hover:border-primary hover:text-primary">{{ $greetingOption }}</button>
                    @endforeach
                    <div class="col-span-2 flex gap-2">
                        <input type="text" wire:model="customGreeting" maxlength="100" placeholder="Greeting custom" class="min-w-0 flex-1 rounded-lg border border-border bg-bg px-2 py-1.5 text-sm text-text placeholder:text-text-muted">
                        <button type="button" wire:click="saveCustomGreeting" class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white">Simpan</button>
                    </div>
                    @error('greeting') <p class="col-span-2 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
            @endif
        </section>

        <section class="{{ $card }} flex min-h-0 flex-1 flex-col items-center justify-center text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-full border-2 border-primary/30 bg-primary/10 text-2xl text-primary" aria-label="Foto profil">
                <i class="fa-regular fa-user" aria-hidden="true"></i>
            </div>
            <p class="mt-2 font-bold">{{ auth()->user()->name }}</p>
            <p class="text-sm text-text-muted">Kelas belum diatur</p>
        </section>
    </div>

    <section
    x-data="{
        selectedDate: null,
        indicator: { left: 0, top: 0, width: 0, height: 0, opacity: 0 },
        selectDate(date, target) {
            this.selectedDate = date;
            this.moveIndicator(target);
        },
        moveIndicator(target) {
            const grid = this.$refs.calendarGrid;
            const gridRect = grid.getBoundingClientRect();
            const targetRect = target.getBoundingClientRect();
            this.indicator = {
                left: targetRect.left - gridRect.left,
                top: targetRect.top - gridRect.top,
                width: targetRect.width,
                height: targetRect.height,
                opacity: 1
            };
        },
        async changeMonth(direction) {
            this.indicator.opacity = 0;
            this.selectedDate = null;
            await $wire.changeCalendarMonth(direction);
        }
    }"
    class="relative mt-3 overflow-hidden rounded-[20px] border-2 border-border bg-surface p-3 text-text shadow-lg shadow-primary/5 sm:col-span-6 sm:p-5"
    aria-label="Kalender tugas"
>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-24 bg-gradient-to-br from-primary/10 via-transparent to-transparent" aria-hidden="true"></div>
    <div class="relative lg:grid lg:grid-cols-2 lg:items-start lg:gap-5">
    <div>
    <div class="relative mb-4 flex flex-col items-center gap-3 text-center sm:flex-row sm:justify-between sm:gap-4 sm:text-left">
        <div class="flex w-full items-start gap-3 text-left sm:w-auto sm:items-center">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-primary text-white shadow-md shadow-primary/25">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="min-w-0">
                <h2 class="text-sm font-bold sm:text-base">Kalender Deadline</h2>
                <p class="text-[10px] text-text-muted sm:text-xs">Jadwal dan tugas dalam satu tampilan</p>
            </div>
        </div>
        <div class="flex w-full max-w-64 items-center justify-between gap-2 rounded-full border border-border/70 bg-bg/70 p-1 shadow-sm sm:w-auto sm:max-w-none">
            <button type="button" x-on:click="changeMonth(-1)" aria-label="Bulan sebelumnya"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-text-muted transition duration-300 hover:bg-primary hover:text-white hover:shadow-md hover:shadow-primary/20 active:scale-90">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
            <span class="min-w-0 flex-1 text-center text-xs font-bold capitalize sm:min-w-28 sm:text-sm">{{ $calendarMonthLabel }}</span>
            <button type="button" x-on:click="changeMonth(1)" aria-label="Bulan berikutnya"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-text-muted transition duration-300 hover:bg-primary hover:text-white hover:shadow-md hover:shadow-primary/20 active:scale-90">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>
    </div>

    <div x-ref="calendarGrid" class="relative grid grid-cols-7 gap-1.5 text-center sm:gap-2">
        <div class="pointer-events-none absolute z-0 rounded-xl bg-primary shadow-md shadow-primary/25"
            :style="`left:${indicator.left}px;top:${indicator.top}px;width:${indicator.width}px;height:${indicator.height}px;opacity:${indicator.opacity};transition:left .35s cubic-bezier(.34,1.56,.64,1),top .35s cubic-bezier(.34,1.56,.64,1),width .35s cubic-bezier(.34,1.56,.64,1),height .35s cubic-bezier(.34,1.56,.64,1),opacity .15s ease`"
            aria-hidden="true"></div>
        @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $weekday)
            <div class="relative z-10 py-1.5 text-[10px] font-bold uppercase tracking-wider text-text-muted sm:text-xs">{{ $weekday }}</div>
        @endforeach

        @foreach ($calendarDays as $day)
            @php
                $dateKey = $day->toDateString();
                $dayTasks = $calendarTasks->get($dateKey, collect());
                $isCurrentMonth = $day->month === $calendarMonth;
                $isToday = $day->isToday();
            @endphp
            <button type="button" wire:key="calendar-day-{{ $dateKey }}" wire:click="selectCalendarDate('{{ $dateKey }}')"
                x-on:click="selectDate('{{ $dateKey }}', $event.currentTarget)"
                aria-label="{{ $day->translatedFormat('l, d F Y') }}{{ $dayTasks->isNotEmpty() ? ', ada '.$dayTasks->count().' tugas' : '' }}"
                x-bind:aria-pressed="selectedDate === '{{ $dateKey }}'"
                class="group relative z-10 flex min-h-10 flex-col items-center justify-center gap-0.5 rounded-xl text-xs transition duration-300 hover:-translate-y-0.5 hover:bg-primary/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary sm:min-h-12 sm:text-sm"
                x-bind:class="selectedDate === '{{ $dateKey }}' ? 'font-bold text-white hover:bg-primary' : '{{ $isCurrentMonth ? 'text-text' : 'text-text-muted/40' }}'">
                <span class="relative">
                    @if ($isToday)
                        <span class="absolute -inset-1.5 rounded-full border border-primary/50" x-bind:class="selectedDate === '{{ $dateKey }}' ? 'border-white/60' : ''" aria-hidden="true"></span>
                    @endif
                    <span class="relative">{{ $day->day }}</span>
                </span>
                @if ($dayTasks->isNotEmpty())
                    <span class="h-1 w-1 rounded-full bg-danger" x-bind:class="selectedDate === '{{ $dateKey }}' ? 'bg-white' : ''" aria-hidden="true"></span>
                @endif
            </button>
        @endforeach
    </div>
    </div>

        <div @if ($selectedCalendarDate) wire:key="calendar-details-{{ $selectedCalendarDate->toDateString() }}" wire:transition.opacity.duration.300ms @endif class="{{ $selectedCalendarDate ? 'block' : 'hidden lg:block' }} mt-4 space-y-3 rounded-2xl border border-border/70 bg-bg/60 p-3 sm:p-4 lg:mt-0" aria-live="polite">
            @if ($selectedCalendarDate)
                <h3 class="text-sm font-bold capitalize sm:text-base">{{ $selectedCalendarDate->translatedFormat('l, d F Y') }}</h3>
            @endif

            <div>
                <h4 class="mb-1 text-xs font-semibold text-text-muted">Jadwal</h4>
                <div class="space-y-1.5">
                    @if ($selectedCalendarDate)
                        @forelse ($selectedDaySchedules as $schedule)
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-border/60 bg-surface px-3 py-2 text-xs shadow-sm">
                                <span class="flex min-w-0 items-center gap-2 truncate font-medium"><i class="fa-regular fa-clock text-primary"></i>{{ $schedule->subject?->name }}</span>
                                <span class="shrink-0 font-semibold text-text-muted">{{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</span>
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-border px-3 py-2 text-xs text-text-muted">Tidak ada jadwal.</p>
                        @endforelse
                    @else
                        <p class="rounded-xl border border-dashed border-border px-3 py-2 text-xs text-text-muted">Tidak ada jadwal.</p>
                    @endif
                </div>
            </div>

            <div>
                <h4 class="mb-1 text-xs font-semibold text-text-muted">Deadline Tugas</h4>
                <div class="space-y-1.5">
                    @if ($selectedCalendarDate)
                        @forelse ($selectedDateTasks as $task)
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-border/60 bg-surface px-3 py-2 text-xs shadow-sm">
                                <span class="flex min-w-0 items-center gap-2 truncate font-medium"><i class="fa-solid fa-circle-check text-primary"></i>{{ $task->title }}</span>
                                <span class="shrink-0 rounded-full bg-primary/10 px-2 py-1 font-bold text-primary">{{ $task->progress }}%</span>
                            </div>
                        @empty
                            <p class="rounded-xl border border-dashed border-border px-3 py-2 text-xs text-text-muted">Tidak ada deadline tugas.</p>
                        @endforelse
                    @else
                        <p class="rounded-xl border border-dashed border-border px-3 py-2 text-xs text-text-muted">Tidak ada deadline tugas.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
</div>
