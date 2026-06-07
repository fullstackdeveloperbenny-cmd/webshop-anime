<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Anime Webshop</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex h-screen bg-gray-100">

<x-admin.sidebar />

<div class="flex-1 flex flex-col">
    <x-admin.header />

    <main class="p-8 flex-1 overflow-auto">
        {{ $slot }}
    </main>
</div>

</body>
</html>
