<?php

use App\Models\Budget;
use App\Models\FinanceRecord;
use App\Models\Schedule;
use App\Models\Task;
use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        // --- Tugas ---
        $totalTasks = Task::count();
        $completed = Task::where('progress', '>=', 100)->count();
        $notStarted = Task::where('progress', 0)->count();
        $inProgress = Task::where('progress', '>', 0)->where('progress', '<', 100)->count();

        $nearestTasks = Task::with('subject')
            ->where('progress', '<', 100)
            ->orderBy('deadline')
            ->orderBy('deadline_time')
            ->limit(5)
            ->get();

        // --- Jadwal hari ini ---
        $hariIni = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][now()->dayOfWeekIso - 1];
        $allToday = Schedule::with('subject')->where('day', $hariIni)->orderBy('start_time')->get();

        // --- Keuangan bulan berjalan ---
        $monthQuery = fn () => FinanceRecord::whereYear('date', now()->year)->whereMonth('date', now()->month);
        $totalIncome = (float) $monthQuery()->where('type', 'income')->sum('amount');
        $totalExpense = (float) $monthQuery()->where('type', 'expense')->sum('amount');
        $recentRecords = $monthQuery()->orderByDesc('date')->orderByDesc('id')->limit(5)->get();

        $budget = Budget::where('month', now()->month)->where('year', now()->year)->first()
            ?? Budget::orderByDesc('year')->orderByDesc('month')->first();
        $budgetAmount = (float) ($budget?->amount ?? 0);

        return [
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

<div class="grid grid-cols-1 sm:grid-cols-6 gap-2.5 text-text">

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

    {{-- 3. Tugas (kolom 1-4, baris 2-4) --}}
    <section class="{{ $card }} sm:col-span-4 sm:row-start-2 sm:row-span-3 sm:min-h-[22rem] flex flex-col gap-3">
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
                <div class="columns-1 md:columns-2 gap-3 [&>*]:mb-3 [&>*]:break-inside-avoid">
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

    {{-- 4. Transaksi (kolom 5-6, baris 2-4) --}}
    <section class="{{ $card }} sm:col-start-5 sm:col-span-2 sm:row-start-2 sm:row-span-3 sm:min-h-[22rem] flex flex-col gap-3">
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
</div>