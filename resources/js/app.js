function applyTheme() {
    document.documentElement.classList.toggle('dark', localStorage.getItem('theme') !== 'light');
}
applyTheme();
document.addEventListener('livewire:navigated', applyTheme);