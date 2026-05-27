<!DOCTYPE html>
<html lang="en" x-data="{ dark: localStorage.getItem('theme') === 'dark' }" x-init="$watch('dark', v => localStorage.setItem('theme', v ? 'dark' : 'light'))" :class="{ 'dark': dark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CampusHub') — CampusHub</title>

    {{-- Google Fonts: Poppins --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        campus: {
                            pink:       '#d04f99',
                            'pink-h':   '#b83d87',
                            'pink-l':   '#f6e6ee',
                            'pink-p':   '#fdedc9',
                            teal:       '#8acfd1',
                            'teal-h':   '#5ab8bb',
                            'teal-l':   '#b2e1eb',
                            yellow:     '#fbe2a7',
                            'yellow-h': '#f5c96b',
                            dark:       '#12242e',
                            'dark-c':   '#1c2e38',
                            'dark-m':   '#24272b',
                        }
                    },
                    boxShadow: {
                        'offset':    '3px 3px 0px rgba(208,79,153,0.45)',
                        'offset-lg': '5px 5px 0px rgba(208,79,153,0.35)',
                        'offset-tl': '3px 3px 0px rgba(138,207,209,0.5)',
                        'offset-dk': '3px 3px 0px rgba(50,72,89,0.7)',
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .campus-card {
            background: #ffffff;
            border: 1.5px solid #e8d0e0;
            border-radius: 1rem;
            box-shadow: 3px 3px 0px rgba(208,79,153,0.2);
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .campus-card:hover { box-shadow: 5px 5px 0px rgba(208,79,153,0.3); transform: translateY(-1px); }
        .dark .campus-card { background: #1c2e38; border-color: #324859; box-shadow: 3px 3px 0px rgba(50,72,89,0.7); }
        .campus-btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: #d04f99; color: #fff;
            padding: 0.6rem 1.4rem; border-radius: 0.6rem;
            font-weight: 600; font-size: 0.875rem;
            border: 1.5px solid #d04f99;
            box-shadow: 3px 3px 0px rgba(208,79,153,0.4);
            transition: all 0.15s;
        }
        .campus-btn:hover { background: #b83d87; border-color: #b83d87; box-shadow: 1px 1px 0px rgba(208,79,153,0.4); transform: translate(2px,2px); }
        .campus-btn-outline {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: transparent; color: #d04f99;
            padding: 0.6rem 1.4rem; border-radius: 0.6rem;
            font-weight: 600; font-size: 0.875rem;
            border: 1.5px solid #d04f99;
            box-shadow: 3px 3px 0px rgba(208,79,153,0.2);
            transition: all 0.15s;
        }
        .campus-btn-outline:hover { background: #f6e6ee; box-shadow: 1px 1px 0px rgba(208,79,153,0.2); transform: translate(2px,2px); }
        .campus-input {
            width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem;
            border: 1.5px solid #e8d0e0; background: #fdf8fb;
            color: #333; font-family: 'Poppins', sans-serif; font-size: 0.875rem;
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }
        .campus-input:focus { border-color: #d04f99; box-shadow: 0 0 0 3px rgba(208,79,153,0.15); }
        .dark .campus-input { background: #1c2e38; border-color: #324859; color: #f3e3ea; }
        .dark .campus-input:focus { border-color: #fbe2a7; box-shadow: 0 0 0 3px rgba(251,226,167,0.15); }
    </style>
</head>

<body class="bg-campus-pink-l dark:bg-campus-dark text-gray-800 dark:text-campus-pink-l transition-colors duration-200 min-h-screen">

{{-- Navbar --}}
<nav class="bg-white dark:bg-campus-dark-c border-b-2 border-campus-pink dark:border-campus-dark-c sticky top-0 z-50 shadow-offset">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="w-8 h-8 rounded-lg bg-campus-pink flex items-center justify-center shadow-offset-tl group-hover:scale-105 transition-transform">
                    <span class="text-white font-black text-sm">C</span>
                </div>
                <span class="font-black text-lg text-gray-900 dark:text-white tracking-tight">Campus<span class="text-campus-pink">Hub</span></span>
            </a>

            {{-- Nav Links --}}
            <div class="hidden md:flex items-center gap-1">
                @auth
                    @if(auth()->user()->hasRole('student'))
                        <a href="{{ route('student.dashboard') }}"
                            class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('student.dashboard') ? 'bg-campus-pink-l text-campus-pink dark:bg-campus-dark-m dark:text-campus-yellow' : 'text-gray-600 dark:text-gray-300 hover:bg-campus-pink-l dark:hover:bg-campus-dark-m hover:text-campus-pink dark:hover:text-campus-yellow' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('student.requests.index') }}"
                            class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('student.requests.*') ? 'bg-campus-pink-l text-campus-pink dark:bg-campus-dark-m dark:text-campus-yellow' : 'text-gray-600 dark:text-gray-300 hover:bg-campus-pink-l dark:hover:bg-campus-dark-m hover:text-campus-pink dark:hover:text-campus-yellow' }}">
                            My Requests
                        </a>
                        <a href="{{ route('student.requests.create') }}" class="campus-btn text-xs py-2">
                            + New Request
                        </a>
                        <a href="{{ route('student.tickets.index') }}"
                            class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('student.tickets.*') ? 'bg-campus-pink-l text-campus-pink dark:bg-campus-dark-m dark:text-campus-yellow' : 'text-gray-600 dark:text-gray-300 hover:bg-campus-pink-l dark:hover:bg-campus-dark-m hover:text-campus-pink dark:hover:text-campus-yellow' }}">
                            Support
                        </a>
                    @elseif(auth()->user()->hasRole('provider'))
                        <a href="{{ route('provider.dashboard') }}"
                            class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('provider.dashboard') ? 'bg-campus-pink-l text-campus-pink' : 'text-gray-600 dark:text-gray-300 hover:bg-campus-pink-l hover:text-campus-pink' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('provider.jobs.index') }}"
                            class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('provider.jobs.*') ? 'bg-campus-pink-l text-campus-pink' : 'text-gray-600 dark:text-gray-300 hover:bg-campus-pink-l hover:text-campus-pink' }}">
                            My Jobs
                        </a>
                    @endif
                @endauth
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-3">
                {{-- Dark Mode Toggle --}}
                <button @click="dark = !dark"
                    class="w-9 h-9 rounded-xl border-2 border-campus-pink/30 flex items-center justify-center text-gray-500 dark:text-campus-yellow hover:border-campus-pink transition-colors">
                    <svg x-show="!dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                @auth
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl border-2 border-campus-pink/30 hover:border-campus-pink transition-colors">
                        <div class="w-7 h-7 rounded-lg bg-campus-pink flex items-center justify-center">
                            <span class="text-white font-bold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 hidden sm:block max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                        <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-52 campus-card overflow-hidden z-50 p-1">
                        <div class="px-3 py-2 border-b border-campus-pink/20 mb-1">
                            <p class="text-xs font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        @if(auth()->user()->hasAnyRole(['super_admin','admin']))
                        <a href="{{ auth()->user()->hasRole('super_admin') ? '/super-admin' : '/admin' }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-campus-pink-l dark:hover:bg-campus-dark-m rounded-lg transition-colors font-medium">
                            <svg class="w-4 h-4 text-campus-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Admin Panel
                        </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="campus-btn-outline text-xs py-2">Sign In</a>
                <a href="{{ route('register') }}" class="campus-btn text-xs py-2">Get Started</a>
                @endauth
            </div>

        </div>
    </div>
</nav>

{{-- Flash messages --}}
@foreach (['success' => ['bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700 text-green-800 dark:text-green-200', '✅'],
           'error'   => ['bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700 text-red-800 dark:text-red-200', '❌'],
           'info'    => ['bg-campus-teal-l/50 dark:bg-campus-dark-m border-campus-teal dark:border-campus-teal/40 text-teal-800 dark:text-campus-teal', 'ℹ️'],
           'warning' => ['bg-campus-yellow/40 dark:bg-yellow-900/20 border-yellow-400 dark:border-yellow-700 text-yellow-800 dark:text-yellow-200', '⚠️'],
          ] as $type => [$classes, $icon])
    @if(session($type))
    <div x-data="{ show: true }" x-show="show" x-transition class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="flex items-center justify-between {{ $classes }} border-2 px-4 py-3 rounded-xl text-sm font-medium" style="border-left-width:4px">
            <span>{{ $icon }} {{ session($type) }}</span>
            <button @click="show = false" class="ml-4 opacity-50 hover:opacity-100 text-lg leading-none">&times;</button>
        </div>
    </div>
    @endif
@endforeach

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
</main>

<footer class="mt-16 border-t-2 border-campus-pink/20 dark:border-campus-dark-c py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded bg-campus-pink flex items-center justify-center">
                <span class="text-white font-black text-xs">C</span>
            </div>
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Campus<span class="text-campus-pink">Hub</span></span>
        </div>
        <p class="text-xs text-gray-400">&copy; {{ date('Y') }} CampusHub — Smart Campus Service Platform</p>
        <div class="flex gap-4 text-xs text-gray-400">
            <span class="text-campus-pink font-semibold">✦ Powered by students</span>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
