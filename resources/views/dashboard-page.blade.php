<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-bg {{ session('dark_mode', true) ? 'dark' : '' }} pb-24 pt-20 sm:pt-6 sm:pb-24">
    <livewire:nav />
    <div class="max-w-7xl mx-auto px-4">
        <livewire:dashboard />
    </div>
</body>
</html>