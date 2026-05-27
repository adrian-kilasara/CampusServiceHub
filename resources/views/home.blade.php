@extends('layouts.app')
@section('title', 'CampusHub')

@section('content')

{{-- Hero --}}
<div class="relative rounded-3xl overflow-hidden mb-12" style="background: linear-gradient(135deg, #d04f99 0%, #8acfd1 50%, #fbe2a7 100%);">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>
    <div class="relative text-center py-20 px-6">
        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-4 py-1.5 rounded-full mb-6 border border-white/30">
            ✦ Smart Campus Platform
        </div>
        <h1 class="text-5xl sm:text-6xl font-black text-white mb-4 leading-tight drop-shadow-lg">
            {{ $siteName }}
        </h1>
        <p class="text-xl text-white/90 mb-10 max-w-2xl mx-auto font-medium drop-shadow">{{ $tagline }}</p>
        <div class="flex gap-4 justify-center flex-wrap">
            @auth
                @if(auth()->user()->hasRole('student'))
                    <a href="{{ route('student.requests.create') }}"
                        class="bg-white text-campus-pink px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-campus-yellow transition-all shadow-offset-tl border-2 border-white">
                        🎓 Request a Service →
                    </a>
                @elseif(auth()->user()->hasRole('provider'))
                    <a href="{{ route('provider.dashboard') }}"
                        class="bg-white text-campus-pink px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-campus-yellow transition-all shadow-offset-tl border-2 border-white">
                        💼 Go to Dashboard →
                    </a>
                @endif
            @else
                <a href="{{ route('register') }}"
                    class="bg-white text-campus-pink px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-campus-yellow transition-all shadow-offset-tl border-2 border-white">
                    🎓 Get Started — Student
                </a>
                <a href="{{ route('register.provider') }}"
                    class="bg-white/20 backdrop-blur-sm text-white border-2 border-white/60 px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-white/30 transition-all">
                    💼 Join as Provider
                </a>
            @endauth
        </div>
    </div>
</div>

{{-- Service Categories --}}
<div class="mb-16">
    <div class="text-center mb-10">
        <span class="inline-block bg-campus-pink-l dark:bg-campus-dark-m text-campus-pink dark:text-campus-yellow text-xs font-bold px-3 py-1 rounded-full border border-campus-pink/20 mb-3">Available Services</span>
        <h2 class="text-3xl font-black text-gray-900 dark:text-white">What can we help with?</h2>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
            $categoryIcons = ['Printing' => '🖨️', 'Tech Repair' => '💻', 'Delivery' => '🚚', 'Food' => '🍔', 'Tutoring' => '📚', 'Creative' => '🎨'];
            $palette = [
                ['#fdedc9','#f5c96b','#b8860b'],
                ['#b2e1eb','#8acfd1','#0e7490'],
                ['#f8d8ea','#d04f99','#9d174d'],
                ['#d1fae5','#6ee7b7','#065f46'],
                ['#ede9fe','#c4b5fd','#5b21b6'],
                ['#fef3c7','#fcd34d','#92400e'],
            ];
        @endphp
        @foreach($categories as $i => $category)
        @php [$bg, $border, $text] = $palette[$i % 6]; @endphp
        <div class="rounded-2xl p-5 text-center hover:-translate-y-1 transition-all duration-200 cursor-pointer border-2"
             style="background:{{ $bg }};border-color:{{ $border }};box-shadow:3px 3px 0 {{ $border }}80;">
            <div class="text-3xl mb-2">{{ $categoryIcons[$category->name] ?? '📦' }}</div>
            <p class="font-bold text-sm" style="color:{{ $text }}">{{ $category->name }}</p>
            <p class="text-xs mt-1 font-medium opacity-60" style="color:{{ $text }}">{{ $category->services_count }} service{{ $category->services_count !== 1 ? 's' : '' }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- How it works --}}
<div class="campus-card p-10">
    <div class="text-center mb-10">
        <span class="inline-block bg-campus-teal-l dark:bg-campus-dark-m text-campus-teal-h dark:text-campus-teal text-xs font-bold px-3 py-1 rounded-full border border-campus-teal/30 mb-3">Simple Process</span>
        <h2 class="text-3xl font-black text-gray-900 dark:text-white">How It Works</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        @foreach([
            ['1','🎓','Register','Create a free student account in seconds.','#d04f99'],
            ['2','🔍','Browse Services','Pick from printing, repair, delivery and more.','#8acfd1'],
            ['3','📝','Submit Request','Describe what you need and set urgency.','#fbe2a7'],
            ['4','✅','Track & Pay','Track status live and pay when done.','#d04f99'],
        ] as [$num, $icon, $title, $desc, $color])
        <div class="text-center relative">
            <div class="absolute -top-1 right-[calc(50%-2.5rem)] w-5 h-5 rounded-full flex items-center justify-center text-white font-black text-xs z-10"
                 style="background:{{ $color }};box-shadow:2px 2px 0 {{ $color }}60">{{ $num }}</div>
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 border-2"
                 style="background:{{ $color }}20;border-color:{{ $color }}50;box-shadow:3px 3px 0 {{ $color }}30">{{ $icon }}</div>
            <h3 class="font-bold text-gray-900 dark:text-white mb-1">{{ $title }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $desc }}</p>
        </div>
        @endforeach
    </div>
</div>

@endsection
