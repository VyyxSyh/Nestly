<?php

use App\Models\Schedule;
use App\Models\Subject;
use Livewire\Component;

new class extends Component
{
    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingScheduleId = null;

    public $subject_id = '';
    public $day = 'Senin';
    public $start_time = '';
    public $end_time = '';
    public $room = '';
    public $lecturer = '';
    public $accent_color = '';

    public array $accentPalette = [
        '#E85D68', '#F07845', '#E7C23B', '#4CAF72',
        '#35B9C4', '#4D83D1', '#8666D5', '#E7659A',
    ];

    public array $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public ?int $confirmingDeleteId = null;

    public function openCreateModal()
    {
        $this->reset(['subject_id', 'start_time', 'end_time', 'room', 'lecturer', 'accent_color', 'editingScheduleId']);
        $this->day = 'Senin';
        $this->accent_color = $this->accentPalette[array_rand($this->accentPalette)];
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);

        $this->editingScheduleId = $schedule->id;
        $this->subject_id = $schedule->subject_id;
        $this->day = $schedule->day;
        $this->start_time = $schedule->start_time;
        $this->end_time = $schedule->end_time;
        $this->room = $schedule->room;
        $this->lecturer = $schedule->lecturer;
        $this->accent_color = $schedule->accent_color;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->isEditing = false;
        $this->editingScheduleId = null;
    }

    public function selectColor(string $color)
    {
        $this->accent_color = $color;
    }

    public function randomizeColor()
    {
        $this->accent_color = $this->accentPalette[array_rand($this->accentPalette)];
    }

    public function save()
    {
        $validated = $this->validate([
            'subject_id' => 'required|exists:subjects,id',
            'day' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room' => 'nullable|string|max:255',
            'lecturer' => 'nullable|string|max:255',
            'accent_color' => 'required|string',
        ]);

        if ($this->isEditing && $this->editingScheduleId) {
            Schedule::findOrFail($this->editingScheduleId)->update($validated);
        } else {
            Schedule::create($validated);
        }

        $this->closeModal();
    }

    public function confirmDelete(int $scheduleId)
    {
        $this->confirmingDeleteId = $scheduleId;
    }

    public function cancelDelete()
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $scheduleId)
    {
        Schedule::findOrFail($scheduleId)->delete();
        $this->confirmingDeleteId = null;
    }

    public function with(): array
    {
        $dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        $schedules = Schedule::with('subject')->get()
            ->sortBy(fn ($s) => array_search($s->day, $dayOrder) * 1440 + (int) str_replace(':', '', substr($s->start_time, 0, 5)));

        return [
            'schedules' => $schedules,
            'subjects' => Subject::all(),
        ];
    }
};
?>

<div>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Jadwal Kuliah</h1>
        <button wire:click="openCreateModal" class="bg-teal-600 text-white px-4 py-2 rounded-md">
            + Tambah Jadwal
        </button>
    </div>

    <div class="space-y-3">
        @forelse ($schedules as $schedule)
            <div class="border rounded-md p-4 border-l-4" style="border-left-color: {{ $schedule->subject->accent_color }}">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-medium">{{ $schedule->subject->name }}</div>
                        <div class="text-sm text-gray-500">
                            {{ $schedule->day }}, {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                        </div>
                        <div class="text-sm text-gray-500">
                            @if ($schedule->room) Ruang {{ $schedule->room }} @endif
                            @if ($schedule->lecturer) — {{ $schedule->lecturer }} @endif
                        </div>
                    </div>
                    <div class="flex gap-3 text-lg">
                        <button wire:click="openEditModal({{ $schedule->id }})" class="text-blue-600" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button wire:click="confirmDelete({{ $schedule->id }})" class="text-red-600" title="Hapus">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>

                @if ($confirmingDeleteId === $schedule->id)
                    <div class="mt-3 bg-red-50 border border-red-200 rounded-md p-3 flex justify-between items-center">
                        <span class="text-sm text-red-700">Yakin mau hapus jadwal ini?</span>
                        <div class="flex gap-2">
                            <button wire:click="cancelDelete" class="text-sm px-3 py-1 rounded-md border">Batal</button>
                            <button wire:click="delete({{ $schedule->id }})" class="text-sm px-3 py-1 rounded-md bg-red-600 text-white">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-500">Belum ada jadwal.</p>
        @endforelse
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-md p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
                <h2 class="text-lg font-semibold mb-4">{{ $isEditing ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h2>

                <form wire:submit="save" class="space-y-3">
                    <div>
                        <label class="block text-sm mb-1">Mata Kuliah</label>
                        <select wire:model="subject_id" class="w-full border rounded-md px-3 py-2">
                            <option value="">-- Pilih Mata Kuliah --</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Hari</label>
                        <select wire:model="day" class="w-full border rounded-md px-3 py-2">
                            @foreach ($days as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Jam Mulai</label>
                            <input type="time" wire:model="start_time" class="w-full border rounded-md px-3 py-2">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Jam Selesai</label>
                            <input type="time" wire:model="end_time" class="w-full border rounded-md px-3 py-2">
                        </div>
                    </div>
                    @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    <div>
                        <label class="block text-sm mb-1">Ruangan</label>
                        <input type="text" wire:model="room" class="w-full border rounded-md px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Dosen</label>
                        <input type="text" wire:model="lecturer" class="w-full border rounded-md px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Warna Aksen</label>
                        <div class="flex gap-2 items-center">
                            @foreach ($accentPalette as $color)
                                <button type="button" wire:click="selectColor('{{ $color }}')"
                                        class="w-7 h-7 rounded-full border-2 {{ $accent_color === $color ? 'border-black' : 'border-transparent' }}"
                                        style="background-color: {{ $color }}"></button>
                            @endforeach
                            <button type="button" wire:click="randomizeColor" class="text-xs text-gray-500 ml-2">
                                <i class="fa-solid fa-shuffle"></i> Acak
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-md border">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-md bg-teal-600 text-white">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>