<?php

use App\Models\Task;
use App\Models\Schedule;
use App\Models\FinanceRecord;
use App\Models\Budget;
use Livewire\Component;

new class extends Component
{
    public function with(): array
    {
        $tasks = Task::all();
        $totalTasks = $tasks->count();
        $notStarted = $tasks->filter(fn ($t) => $t->status === 'not_started')->count();
        $inProgress = $tasks->filter(fn ($t) => $t->status === 'in_progress')->count();
        $completed = $tasks->filter(fn ($t) => $t->status === 'completed')->count();
        $overallProgress = $totalTasks > 0 ? round($tasks->avg('progress')) : 0;

        $nearestDeadlines = Task::where('progress', '<', 100)
            ->orderBy('deadline')
            ->limit(5)
            ->get();

        $todaySchedules = Schedule::with('subject')
            ->where('day', now()->translatedFormat('l') === 'Monday' ? 'Senin' : $this->mapDayToIndonesian(now()->dayOfWeekIso))
            ->orderBy('start_time')
            ->get();

        $currentMonthRecords = FinanceRecord::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->get();

        $totalIncome = $currentMonthRecords->where('type', 'income')->sum('amount');
        $totalExpense = $currentMonthRecords->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        $budget = Budget::where('month', now()->month)->where('year', now()->year)->first()
            ?? Budget::orderByDesc('year')->orderByDesc('month')->first();
        $budgetAmount = $budget?->amount ?? 0;

        return [
            'totalTasks' => $totalTasks,
            'notStarted' => $notStarted,
            'inProgress' => $inProgress,
            'completed' => $completed,
            'overallProgress' => $overallProgress,
            'nearestDeadlines' => $nearestDeadlines,
            'todaySchedules' => $todaySchedules,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netBalance' => $netBalance,
            'budgetAmount' => $budgetAmount,
        ];
    }

    private function mapDayToIndonesian(int $isoDay): string
    {
        return match ($isoDay) {
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        };
    }
};
?>

<div class="space-y-6">
    <h1 class="text-xl font-semibold">Dashboard</h1>

    {{-- Task summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="border rounded-md p-3">
            <div class="text-sm text-gray-500">Total Tugas</div>
            <div class="text-2xl font-semibold">{{ $totalTasks }}</div>
        </div>
        <div class="border rounded-md p-3">
            <div class="text-sm text-gray-500">Not Started</div>
            <div class="text-2xl font-semibold text-gray-600">{{ $notStarted }}</div>
        </div>
        <div class="border rounded-md p-3">
            <div class="text-sm text-gray-500">In Progress</div>
            <div class="text-2xl font-semibold text-yellow-600">{{ $inProgress }}</div>
        </div>
        <div class="border rounded-md p-3">
            <div class="text-sm text-gray-500">Completed</div>
            <div class="text-2xl font-semibold text-green-600">{{ $completed }}</div>
        </div>
    </div>

    <div>
        <div class="flex justify-between text-sm mb-1">
            <span>Progress Keseluruhan</span>
            <span>{{ $overallProgress }}%</span>
        </div>
        <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full bg-teal-500" style="width: {{ $overallProgress }}%"></div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        {{-- Nearest deadlines --}}
        <div>
            <h2 class="font-medium mb-2">Deadline Terdekat</h2>
            <div class="space-y-2">
                @forelse ($nearestDeadlines as $task)
                    <div class="border rounded-md p-3 border-l-4"
                         style="border-left-color:
                             @if($task->urgency_color === 'red') #E85D68
                             @elseif($task->urgency_color === 'orange') #F07845
                             @elseif($task->urgency_color === 'yellow') #E7C23B
                             @else #4CAF72
                             @endif">
                        <div class="font-medium text-sm">{{ $task->title }}</div>
                        <div class="text-xs text-gray-500">{{ $task->deadline->format('d M Y, H:i') }}</div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Tidak ada tugas mendatang.</p>
                @endforelse
            </div>
        </div>

        {{-- Today's schedule --}}
        <div>
            <h2 class="font-medium mb-2">Jadwal Hari Ini</h2>
            <div class="space-y-2">
                @forelse ($todaySchedules as $schedule)
                    <div class="border rounded-md p-3 border-l-4" style="border-left-color: {{ $schedule->accent_color }}">
                        <div class="font-medium text-sm">{{ $schedule->subject->name }}</div>
                        <div class="text-xs text-gray-500">
                            {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                            @if ($schedule->room) — Ruang {{ $schedule->room }} @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Tidak ada jadwal hari ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Finance summary --}}
    <div>
        <h2 class="font-medium mb-2">Ringkasan Keuangan — {{ now()->translatedFormat('F Y') }}</h2>
        <div class="grid grid-cols-3 gap-3">
            <div class="border rounded-md p-3">
                <div class="text-sm text-gray-500">Pemasukan</div>
                <div class="font-semibold text-green-600">Rp{{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
            <div class="border rounded-md p-3">
                <div class="text-sm text-gray-500">Pengeluaran</div>
                <div class="font-semibold text-red-600">Rp{{ number_format($totalExpense, 0, ',', '.') }}</div>
            </div>
            <div class="border rounded-md p-3">
                <div class="text-sm text-gray-500">Sisa Saldo</div>
                <div class="font-semibold {{ $netBalance < 0 ? 'text-red-600' : 'text-gray-800' }}">
                    Rp{{ number_format($netBalance, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
</div>