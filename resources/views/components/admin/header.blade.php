<header class="h-16 bg-white shadow-sm flex items-center px-8 justify-between border-b">
    <h2 class="font-semibold text-gray-800">Beheerpaneel</h2>
    <div class="text-sm text-gray-500">
        {{ Auth::user()->name ?? 'Admin' }}
    </div>
</header>
