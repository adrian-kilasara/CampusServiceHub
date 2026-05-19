<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('dark') === 'true' }" :class="darkMode ? 'dark' : ''" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CampusHub') — Smart Campus Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-200">

{{-- Navbar --}}
<nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">CH</span>
                    </div>
                    <span class="font-semibold text-lg text-gray-900 dark:text-white">CampusHub</span>
                </a>
                @auth
                    @if(auth()->user()->hasRole('student'))
                        <div class="hidden md:flex gap-6">
                            <a href="{{ route('student.dashboard') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Dashboard</a>
                            <a href="{{ route('student.requests.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">My Requests</a>
                            <a href="{{ route('student.requests.create') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">New Request</a>
                            <a href="{{ route('student.tickets.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Support</a>
                        </div>
                    @endif
                    @if(auth()->user()->hasRole('provider'))
                        <div class="hidden md:flex gap-6">
                            <a href="{{ route('provider.dashboard') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">Dashboard</a>
                            <a href="{{ route('provider.jobs.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">My Jobs</a>
                        </div>
                    @endif
                @endauth
            </div>

            <div class="flex items-center gap-4">
                {{-- Dark mode toggle --}}
                <button @click="darkMode = !darkMode; localStorage.setItem('dark', darkMode)"
                    class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400">
                            <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                <span class="text-indigo-600 dark:text-indigo-400 font-semibold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                            </div>
                            <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg py-1 z-50">
                            <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Signed in as</p>
                                <p class="text-sm font-medium truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">Sign out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 transition">Login</a>
                    <a href="{{ route('register') }}" class="text-sm font-medium bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">Sign Up</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
@foreach (['success' => 'green', 'error' => 'red', 'info' => 'blue', 'warning' => 'amber'] as $type => $color)
    @if(session($type))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="flex items-center justify-between bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20 border border-{{ $color }}-200 dark:border-{{ $color }}-800 text-{{ $color }}-800 dark:text-{{ $color }}-200 px-4 py-3 rounded-xl text-sm">
                <span>{{ session($type) }}</span>
                <button @click="show = false" class="ml-4 opacity-60 hover:opacity-100">&times;</button>
            </div>
        </div>
    @endif
@endforeach

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
</main>

<footer class="mt-16 border-t border-gray-200 dark:border-gray-800 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
    &copy; {{ date('Y') }} CampusHub — Smart Campus Service Platform
</footer>

@stack('scripts')
</body>
</html>
