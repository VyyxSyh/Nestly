<label class="switch" wire:ignore
    x-data="{ dark: document.documentElement.classList.contains('dark') }"
    x-on:theme-changed.window="dark = $event.detail"
    x-on:theme-mode-updated.window="dark = $event.detail.dark">
    <input type="checkbox" class="switch-input" aria-label="Toggle dark mode"
        x-bind:checked="dark"
        x-on:change="
            const isDark = $event.target.checked;
            const root = document.documentElement;
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            window.currentThemeMode = isDark ? 'dark' : 'light';
            root.classList.add('theme-transition');
            root.classList.toggle('dark', isDark);
            clearTimeout(window.themeTimer);
            window.themeTimer = setTimeout(() => root.classList.remove('theme-transition'), 900);
            $dispatch('theme-changed', isDark);
        " />
    <div class="slider">
        <div class="sun-moon">
            <svg class="moon-dot moon-dot-1" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
            <svg class="moon-dot moon-dot-2" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
            <svg class="moon-dot moon-dot-3" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
            <svg class="light-ray light-ray-1" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
            <svg class="light-ray light-ray-2" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
            <svg class="light-ray light-ray-3" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
            <svg class="cloud-dark cloud-1" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
            <svg class="cloud-dark cloud-2" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
            <svg class="cloud-dark cloud-3" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
            <svg class="cloud-light cloud-4" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
            <svg class="cloud-light cloud-5" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
            <svg class="cloud-light cloud-6" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
        </div>
        <div class="stars">
            <svg class="star star-1" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path></svg>
            <svg class="star star-2" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path></svg>
            <svg class="star star-3" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path></svg>
            <svg class="star star-4" viewBox="0 0 20 20"><path d="M 0 10 C 10 10,10 10 ,0 10 C 10 10 , 10 10 , 10 20 C 10 10 , 10 10 , 20 10 C 10 10 , 10 10 , 10 0 C 10 10,10 10 ,0 10 Z"></path></svg>
        </div>
    </div>
</label>
