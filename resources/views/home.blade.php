@extends('layouts.app')
@section('title', 'CampusHub')

@section('content')

{{-- Hero --}}
<div class="text-center py-16">
    <div class="inline-flex items-center gap-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-semibold px-3 py-1 rounded-full mb-6">
        ✦ Smart Campus Platform
    </div>
    <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-4 leading-tight">
        {{ $siteName }}
    </h1>
    <p class="text-xl text-gray-500 dark:text-gray-400 mb-8 max-w-2xl mx-auto">{{ $tagline }}</p>
    <div class="flex gap-4 justify-center flex-wrap">
        @auth
            @if(auth()->user()->hasRole('student'))
                <a href="{{ route('student.requests.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition shadow-lg">
                    Request a Service →
                </a>
            @elseif(auth()->user()->hasRole('provider'))
                <a href="{{ route('provider.dashboard') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition shadow-lg">
                    Go to Dashboard →
                </a>
            @endif
        @else
            <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition shadow-lg">
                Get Started — Student
            </a>
            <a href="{{ route('register.provider') }}" class="border border-indigo-300 dark:border-indigo-700 text-indigo-600 dark:text-indigo-400 px-6 py-3 rounded-xl font-semibold hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition">
                Join as Provider
            </a>
        @endauth
    </div>
</div>

{{-- Service Categories --}}
<div class="mt-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Available Services</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($categories as $category)
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 text-center hover:shadow-lg hover:border-indigo-300 dark:hover:border-indigo-700 transition group cursor-pointer">
                <div class="w-12 h-12 rounded-xl mx-auto mb-3 flex items-center justify-center text-2xl"
                    style="background-color: {{ $category->color }}20;">
                    <span style="color: {{ $category->color }}">●</span>
                </div>
                <p class="font-semibold text-sm text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $category->name }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $category->services_count }} service{{ $category->services_count !== 1 ? 's' : '' }}</p>
            </div>
        @endforeach
    </div>
</div>

{{-- How it works --}}
<div class="mt-20">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">How It Works</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @foreach([
            ['1', 'Register', 'Create a free student account in seconds.'],
            ['2', 'Browse Services', 'Pick from printing, repair, delivery and more.'],
            ['3', 'Submit Request', 'Describe what you need and set urgency.'],
            ['4', 'Track & Pay', 'Track status live and pay when done.'],
        ] as [$step, $title, $desc])
        <div class="text-center">
            <div class="w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold mx-auto mb-4">{{ $step }}</div>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $title }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $desc }}</p>
        </div>
        @endforeach
    </div>
</div>

@endsection
