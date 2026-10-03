<?php

use App\Models\Subject;
use Livewire\Component;

new class extends Component
{
    public bool $isEditing = false;

    public ?int $editingSubjectId = null;

    public bool $showModal = false;

    public $name = '';

    public ?int $confirmingDeleteId = null;

    public $accent_color = '';

    public array $accentPalette = [
        '#ff0026', '#F07845', '#E7C23B', '#4CAF72',
        '#35B9C4', '#4D83D1', '#8666D5', '#E7659A',
    ];

    public function openModal()
    {
        $this->reset(['name', 'editingSubjectId']);
        $this->accent_color = $this->accentPalette[array_rand($this->accentPalette)];
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $id)
    {
        $subject = Subject::where('user_id', auth()->id())->findOrFail($id);
        $this->editingSubjectId = $subject->id;
        $this->name = $subject->name;
        $this->accent_color = $subject->accent_color;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function selectColor(string $color)
    {
        $this->accent_color = $color;
    }

    public function randomizeColor()
    {
        $this->accent_color = $this->accentPalette[array_rand($this->accentPalette)];
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'accent_color' => 'required|string',
        ]);

        if ($this->isEditing && $this->editingSubjectId) {
            Subject::where('user_id', auth()->id())->findOrFail($this->editingSubjectId)->update($validated);
        } else {
            Subject::create([...$validated, 'user_id' => auth()->id()]);
        }

        $this->closeModal();
    }

    public function confirmDelete(int $id)
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id)
    {
        $subject = Subject::where('user_id', auth()->id())->withCount(['tasks', 'schedules'])->findOrFail($id);

        if ($subject->tasks_count > 0 || $subject->schedules_count > 0) {
            session()->flash('deleteError', 'Tidak bisa hapus "'.$subject->name.'" karena masih dipakai di '.$subject->tasks_count.' tugas dan '.$subject->schedules_count.' jadwal.');
            $this->confirmingDeleteId = null;

            return;
        }

        $subject->delete();
        $this->confirmingDeleteId = null;
    }

    public function with(): array
    {
        return [
            'subjects' => Subject::where('user_id', auth()->id())->withCount(['tasks', 'schedules'])->get(),
        ];
    }
};
?>

<div class="text-text">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-semibold">Mata Kuliah</h1>
        <button wire:click="openModal" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-md cursor-pointer transition-colors">
            + Tambah Mata Kuliah
        </button>
    </div>

    @if (session('deleteError'))
        <div class="bg-danger/10 border border-danger/40 text-danger text-sm rounded-md p-3 mb-3">
            {{ session('deleteError') }}
        </div>
    @endif

    <div class="space-y-2">
        @forelse ($subjects as $subject)
            <div class="border border-border bg-surface rounded-md p-3 flex justify-between items-center border-l-4" style="border-left-color: {{ $subject->accent_color }}">
                <div>
                    <span class="font-medium">{{ $subject->name }}</span>
                    <span class="text-sm text-text-muted ml-2">
                        ({{ $subject->tasks_count }} tugas, {{ $subject->schedules_count }} jadwal)
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <button wire:click="openEditModal({{ $subject->id }})" class="text-blue-500">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button wire:click="confirmDelete({{ $subject->id }})" class="text-danger">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>

            @if ($confirmingDeleteId === $subject->id)
                <div class="bg-danger/10 border border-danger/40 rounded-md p-3 flex justify-between items-center -mt-1">
                    <span class="text-sm text-danger">
                        Yakin hapus? Semua tugas/jadwal terkait akan kehilangan referensi mata kuliah ini.
                    </span>
                    <div class="flex gap-2">
                        <button wire:click="cancelDelete" class="text-sm px-3 py-1 rounded-md border border-border text-text">Batal</button>
                        <button wire:click="delete({{ $subject->id }})" class="text-sm px-3 py-1 rounded-md bg-danger text-white">Hapus</button>
                    </div>
                </div>
            @endif
        @empty
            <p class="text-text-muted">Belum ada mata kuliah.</p>
        @endforelse
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-surface text-text rounded-md p-6 w-full max-w-sm">
                <h2 class="text-lg font-semibold mb-4">{{ $isEditing ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}</h2>
                <form wire:submit="save" class="space-y-3">
                    <div>
                        <label class="block text-sm mb-1">Nama Mata Kuliah</label>
                        <input type="text" wire:model="name" class="w-full border border-border bg-bg text-text rounded-md px-3 py-2">
                        @error('name') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Warna Aksen</label>
                        <div class="flex gap-2 items-center flex-wrap">
                            @foreach ($accentPalette as $color)
                                <button type="button" wire:click="selectColor('{{ $color }}')"
                                        class="w-7 h-7 rounded-full border-2 {{ $accent_color === $color ? 'border-text' : 'border-transparent' }}"
                                        style="background-color: {{ $color }}"></button>
                            @endforeach
                            <button type="button" wire:click="randomizeColor" class="text-xs text-text-muted ml-2">
                                <i class="fa-solid fa-shuffle"></i> Acak
                            </button>
                        </div>
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
