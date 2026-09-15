<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Nestly - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
        <nav class="flex gap-4 mb-6 text-sm">
            <a href="/" class="font-medium text-teal-600">Dashboard</a>
            <a href="/tasks" class="text-gray-600">Tugas</a>
            <a href="/schedules" class="text-gray-600">Jadwal</a>
            <a href="/subjects" class="text-gray-600">Mata Kuliah</a>
            <a href="/finance" class="text-gray-600">Keuangan</a>
        </nav>
        <livewire:dashboard />
    </div>
</body>
</html>