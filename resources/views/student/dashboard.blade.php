@extends('layouts.app')
@section('title', 'Student Dashboard')

@section('content')
<div class="mb-8 flex items-center justify-between flex-wrap gap-4">
    <div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white">
            Hey, {{ explode(' ', $user->name)[0] }}! 👋
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Here's what's happening with your requests.</p>
    </div>
    <a href="{{ route('student.requests.create') }}" class="campus-btn">+ New Request</a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['Total Requests', $stats['total'],       '#fdedc9','#f5c96b','#b8860b','📋'],
        ['Pending',        $stats['pending'],      '#f8d8ea','#d04f99','#9d174d','⏳'],
        ['In Progress',    $stats['in_progress'],  '#b2e1eb','#8acfd1','#0e7490','🔧'],
        ['Completed',      $stats['completed'],    '#d1fae5','#6ee7b7','#065f46','✅'],
    ] as [$label, $value, $bg, $border, $text, $icon])
    <div class="rounded-2xl p-5 border-2 hover:-translate-y-0.5 transition-all"
         style="background:{{ $bg }};border-color:{{ $border }};box-shadow:3px 3px 0 {{ $border }}80">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xl">{{ $icon }}</span>
            <span class="text-xs font-bold" style="color:{{ $text }}">{{ $label }}</span>
        </div>
        <p class="text-4xl font-black" style="color:{{ $text }}">{{ $value }}</p>
    </div>
    @endforeach
</div>

{{-- Recent Requests --}}
<div class="campus-card overflow-hidden">
    <div class="px-6 py-4 flex justify-between items-center border-b-2 border-campus-pink/10 dark:border-campus-dark-m"
         style="background:linear-gradient(90deg,rgba(208,79,153,0.05),rgba(138,207,209,0.05))">
        <h2 class="font-black text-gray-900 dark:text-white">📬 Recent Requests</h2>
        <a href="{{ route('student.requests.index') }}" class="text-sm font-bold text-campus-pink dark:text-campus-yellow hover:underline">View all →</a>
    </div>

    @if($requests->isEmpty())
        <div class="px-6 py-16 text-center">
            <p class="text-5xl mb-4">📭</p>
            <p class="font-bold text-gray-700 dark:text-gray-300 mb-1">No requests yet</p>
            <p class="text-sm text-gray-400 mb-6">Submit your first service request to get started.</p>
            <a href="{{ route('student.requests.create') }}" class="campus-btn">Create your first request</a>
        </div>
    @else
        <div class="divide-y divide-campus-pink/10 dark:divide-campus-dark-m">
            @php
                $smap = [
                    'pending'     => ['#fdedc9','#f5c96b','#b8860b'],
                    'accepted'    => ['#b2e1eb','#8acfd1','#0e7490'],
                    'in_progress' => ['#b2e1eb','#8acfd1','#0e7490'],
                    'completed'   => ['#d1fae5','#6ee7b7','#065f46'],
                    'cancelled'   => ['#f3f4f6','#d1d5db','#6b7280'],
                    'disputed'    => ['#fee2e2','#fca5a5','#991b1b'],
                ];
            @endphp
            @foreach($requests as $req)
            @php [$sbg,$sborder,$scolor] = $smap[$req->status] ?? ['#f3f4f6','#d1d5db','#6b7280']; @endphp
            <a href="{{ route('student.requests.show', $req) }}"
                class="flex items-center justify-between px-6 py-4 hover:bg-campus-pink-l/40 dark:hover:bg-campus-dark-m/60 transition group">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center font-black text-sm border-2"
                         style="background:{{ $sbg }};border-color:{{ $sborder }};color:{{ $scolor }}">
                        {{ strtoupper(substr($req->service->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $req->title }}</p>
                        <p class="text-xs text-gray-400">{{ $req->service->name }} • {{ $req->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full border capitalize"
                          style="background:{{ $sbg }};border-color:{{ $sborder }};color:{{ $scolor }}">
                        {{ str_replace('_', ' ', $req->status) }}
                    </span>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-campus-pink transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
