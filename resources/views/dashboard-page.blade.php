<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        document.documentElement.classList.toggle('dark', localStorage.getItem('theme') !== 'light');
    </script>
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-bg pb-24 pt-20 sm:pt-6 sm:pb-24">
    @persist('nav')
        <livewire:nav />
    @endpersist
    <div class="page-content max-w-7xl mx-auto px-4">
        <livewire:dashboard />
    </div>
</body>
</html>
