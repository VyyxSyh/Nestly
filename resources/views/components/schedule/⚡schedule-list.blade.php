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

    public array $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public ?int $confirmingDeleteId = null;

    public function openCreateModal()
    {
        $this->reset(['subject_id', 'start_time', 'end_time', 'room', 'lecturer', 'editingScheduleId']);
        $this->day = 'Senin';
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
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->isEditing = false;
        $this->editingScheduleId = null;
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

<div class="text-text">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Jadwal Kuliah</h1>
        <button wire:click="openCreateModal" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-md cursor-pointer transition-colors">
            + Tambah Jadwal
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6 mt-5 md:mt-6">
        @forelse ($schedules as $schedule)
            @php $accent = $schedule->subject->accent_color; @endphp
                <div class="relative border-2 rounded-2xl px-4 pt-4 pb-3.5 md:py-5 bg-surface"
                style="border-color: {{ $accent }}; box-shadow: 5px 5px 0px {{ $accent }};">

                {{-- Badge Hari --}}
                <div class="absolute -top-3 left-4 bg-surface px-3 py-1 rounded-full border-2 text-xs font-semibold whitespace-nowrap"
                    style="border-color: {{ $accent }}; color: {{ $accent }};">
                    {{ $schedule->day }}
                </div>

                <div class="flex items-start gap-4 mt-2">
                    {{-- Blok waktu boarding pass --}}
                    <div class="flex flex-col items-center pr-3 md:pr-4 border-r-2" style="border-color: {{ $accent }};">
                        <span class="text-2xl md:text-3xl font-bold leading-none" style="color: {{ $accent }};">
                            {{ substr($schedule->start_time, 0, 2) }}
                        </span>
                        <span class="text-xs md:text-sm text-text-muted mt-1">{{ substr($schedule->start_time, 3, 2) }}</span>
                        <div class="w-px h-3 md:h-4 bg-border my-1"></div>
                        <span class="text-xs md:text-sm text-text-muted">{{ substr($schedule->end_time, 0, 5) }}</span>
                    </div>

                    {{-- Info mata kuliah --}}
                    <div class="flex-1">
                        <div class="font-bold text-base md:text-lg mb-1">{{ $schedule->subject->name }}</div>
                        @if ($schedule->room)
                            <div class="text-xs md:text-sm text-text-muted">{{ $schedule->room }}</div>
                        @endif
                        @if ($schedule->lecturer)
                            <div class="text-xs md:text-sm text-text-muted">{{ $schedule->lecturer }}</div>
                        @endif
                    </div>
                </div>

                {{-- Icon Edit/Hapus --}}
                <div class="flex justify-center gap-4 md:gap-5 mt-3 md:mt-4">
                    <button wire:click="openEditModal({{ $schedule->id }})"
                            class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center rounded-lg border-2 border-blue-500 bg-blue-500 text-white cursor-pointer
                                hover:bg-transparent hover:scale-110 hover:shadow-[0_0_10px_rgba(59,130,246,0.7)] hover:text-blue-500
                                active:scale-90 transition-all duration-150">
                        <i class="fa-solid fa-pen-to-square text-sm md:text-base"></i>
                    </button>
                    <button wire:click="confirmDelete({{ $schedule->id }})"
                            class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center rounded-lg border-2 border-red-500 bg-red-500 text-white cursor-pointer
                                hover:bg-transparent hover:scale-110 hover:shadow-[0_0_10px_rgba(239,68,68,0.7)] hover:text-red-500
                                active:scale-90 transition-all duration-150">
                        <i class="fa-solid fa-trash text-sm md:text-base"></i>
                    </button>
                </div>

                @if ($confirmingDeleteId === $schedule->id)
                    <div class="mt-3 bg-danger/10 border border-danger/40 rounded-md p-3 flex justify-between items-center">
                        <span class="text-sm text-danger">Yakin mau hapus jadwal ini?</span>
                        <div class="flex gap-2">
                            <button wire:click="cancelDelete" class="text-sm px-3 py-1 rounded-md border border-border text-text">Batal</button>
                            <button wire:click="delete({{ $schedule->id }})" class="text-sm px-3 py-1 rounded-md bg-danger text-white">Hapus</button>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-text-muted col-span-2">Belum ada jadwal.</p>
        @endforelse
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-surface text-text rounded-md p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
                <h2 class="text-lg font-semibold mb-4">{{ $isEditing ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h2>

                <form wire:submit="save" class="space-y-3">
                    <div>
                        <label class="block text-sm mb-1">Mata Kuliah</label>
                        <select wire:model="subject_id" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2">
                            <option value="">-- Pilih Mata Kuliah --</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Hari</label>
                        <select wire:model="day" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2">
                            @foreach ($days as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Jam Mulai</label>
                            <input type="time" wire:model="start_time" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm mb-1">Jam Selesai</label>
                            <input type="time" wire:model="end_time" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2">
                        </div>
                    </div>
                    @error('end_time') <span class="text-danger text-sm">{{ $message }}</span> @enderror

                    <div>
                        <label class="block text-sm mb-1">Ruangan</label>
                        <input type="text" wire:model="room" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Dosen</label>
                        <input type="text" wire:model="lecturer" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-md border border-border text-text">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-md bg-primary hover:bg-secondary text-white cursor-pointer transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>