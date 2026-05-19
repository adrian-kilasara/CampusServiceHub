@extends('layouts.app')
@section('title', 'Student Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome back, {{ $user->name }} 👋</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">Here's what's happening with your requests.</p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['Total Requests', $stats['total'], 'bg-indigo-50 dark:bg-indigo-900/30', 'text-indigo-600 dark:text-indigo-400'],
        ['Pending', $stats['pending'], 'bg-amber-50 dark:bg-amber-900/30', 'text-amber-600 dark:text-amber-400'],
        ['In Progress', $stats['in_progress'], 'bg-blue-50 dark:bg-blue-900/30', 'text-blue-600 dark:text-blue-400'],
        ['Completed', $stats['completed'], 'bg-green-50 dark:bg-green-900/30', 'text-green-600 dark:text-green-400'],
    ] as [$label, $value, $bg, $color])
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5">
        <div class="{{ $bg }} rounded-lg px-2 py-1 w-fit mb-3">
            <span class="{{ $color }} text-xs font-semibold">{{ $label }}</span>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
    </div>
    @endforeach
</div>

{{-- Quick Action --}}
<div class="mb-8">
    <a href="{{ route('student.requests.create') }}"
        class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition shadow">
        + New Service Request
    </a>
</div>

{{-- Recent Requests --}}
<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
        <h2 class="font-semibold text-gray-900 dark:text-white">Recent Requests</h2>
        <a href="{{ route('student.requests.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View all</a>
    </div>

    @if($requests->isEmpty())
        <div class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
            <p class="text-4xl mb-3">📭</p>
            <p>No requests yet. <a href="{{ route('student.requests.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Create your first one!</a></p>
        </div>
    @else
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($requests as $req)
            <a href="{{ route('student.requests.show', $req) }}" class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $req->title }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $req->service->name }} • {{ $req->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-3">
                    @php
                        $colors = ['pending'=>'amber','accepted'=>'blue','in_progress'=>'blue','completed'=>'green','cancelled'=>'red','disputed'=>'red'];
                        $color = $colors[$req->status] ?? 'gray';
                    @endphp
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 text-{{ $color }}-700 dark:text-{{ $color }}-400 capitalize">
                        {{ str_replace('_', ' ', $req->status) }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
