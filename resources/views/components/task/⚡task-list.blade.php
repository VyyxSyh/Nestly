<?php

use App\Models\Task;
use App\Models\Subject;
use Livewire\Component;

new class extends Component
{
    public bool $showModal = false;

    public $subject_id = '';
    public $title = '';
    public $description = '';
    public $deadline = '';
    public $priority = '';

    public function openModal() {
        $this->reset(['subject_id', 'title', 'description', 'deadline', 'priority']);
        $this->showModal = true;
    }

    public function closeModal() {
        $this->showModal = false;
    }

    public function save() {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'nullable|exists:subjects,id',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'priority' => 'required|in:low,medium,high',
        ]);

        Task::create($validated);

        $this->closeModal();
    }

    public function with(): array {
        return [
            'task' => Task::with('subject')->latest()->get(),
            'subjects' => Subject::all(),
        ];
    }
    
};
?>

<div>
    {{-- Nothing in life is to be feared, it is only to be understood. Now is the time to understand more, so that we may fear less. - Maria Skłodowska-Curie --}}
</div>