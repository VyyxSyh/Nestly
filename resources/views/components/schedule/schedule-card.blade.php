<div class="relative border-2 rounded-2xl px-4 pt-4 pb-3.5 md:py-5 bg-surface mb-1" style="border-color: {{ $accent }}; box-shadow: 5px 5px 0px {{ $accent }};">
    @if ($showDayBadge ?? true)
        <div class="absolute -top-3 left-4 bg-surface px-3 py-1 rounded-full border-2 text-xs font-semibold whitespace-nowrap" style="border-color: {{ $accent }}; color: {{ $accent }};">{{ $schedule->day }}</div>
    @endif
    <div class="flex items-start gap-4 mt-2">
        <div class="flex flex-col items-center pr-3 md:pr-4 border-r-2" style="border-color: {{ $accent }};">
            <span class="text-2xl md:text-3xl font-bold leading-none" style="color: {{ $accent }};">{{ substr($schedule->start_time, 0, 2) }}</span>
            <span class="text-xs md:text-sm text-text-muted mt-1">{{ substr($schedule->start_time, 3, 2) }}</span>
            <div class="w-px h-3 md:h-4 bg-border my-1"></div>
            <span class="text-xs md:text-sm text-text-muted">{{ substr($schedule->end_time, 0, 5) }}</span>
        </div>
        <div class="flex-1">
            <div class="font-bold text-base md:text-lg mb-1">{{ $schedule->subject->name }}</div>
            @if ($schedule->room)<div class="text-xs md:text-sm text-text-muted">{{ $schedule->room }}</div>@endif
            @if ($schedule->lecturer)<div class="text-xs md:text-sm text-text-muted">{{ $schedule->lecturer }}</div>@endif
        </div>
    </div>
    <div class="flex justify-center gap-4 md:gap-5 mt-3 md:mt-4">
        <button wire:click="openEditModal({{ $schedule->id }})" class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center rounded-lg bg-blue-500 text-white"><i class="fa-solid fa-pen-to-square text-sm md:text-base"></i></button>
        <button wire:click="confirmDelete({{ $schedule->id }})" class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center rounded-lg bg-red-500 text-white"><i class="fa-solid fa-trash text-sm md:text-base"></i></button>
    </div>
</div>
