function applyTheme() {
    if (window.currentThemeMode) {
        document.documentElement.classList.toggle('dark', window.currentThemeMode === 'dark');
    }
}
applyTheme();
document.addEventListener('livewire:navigated', applyTheme);
