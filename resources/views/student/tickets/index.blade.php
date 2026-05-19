@extends('layouts.app')
@section('title', 'Support Tickets')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Support Tickets</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Get help from our support team.</p>
    </div>
    <a href="{{ route('student.tickets.create') }}"
        class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-indigo-700 transition shadow text-sm">
        + New Ticket
    </a>
</div>

<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
    @if($tickets->isEmpty())
        <div class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">
            <p class="text-5xl mb-4">🎫</p>
            <p class="font-medium text-gray-700 dark:text-gray-300 mb-1">No tickets yet</p>
            <p class="text-sm mb-6">Need help? Open a support ticket and we'll get back to you.</p>
            <a href="{{ route('student.tickets.create') }}"
                class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-indigo-700 transition text-sm">
                Open Ticket
            </a>
        </div>
    @else
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @php
                $statusColors = ['open'=>'green','in_progress'=>'blue','resolved'=>'gray','closed'=>'gray'];
                $priorityColors = ['low'=>'gray','medium'=>'blue','high'=>'orange','urgent'=>'red'];
            @endphp
            @foreach($tickets as $ticket)
            @php
                $sc = $statusColors[$ticket->status] ?? 'gray';
                $pc = $priorityColors[$ticket->priority] ?? 'gray';
            @endphp
            <a href="{{ route('student.tickets.show', $ticket) }}"
                class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs flex-shrink-0">
                        🎫
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $ticket->subject }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $ticket->ticket_number }} • {{ $ticket->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-{{ $pc }}-100 dark:bg-{{ $pc }}-900/30 text-{{ $pc }}-700 dark:text-{{ $pc }}-400 capitalize hidden sm:inline-flex">
                        {{ $ticket->priority }}
                    </span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-{{ $sc }}-100 dark:bg-{{ $sc }}-900/30 text-{{ $sc }}-700 dark:text-{{ $sc }}-400 capitalize">
                        {{ str_replace('_', ' ', $ticket->status) }}
                    </span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </a>
            @endforeach
        </div>
        @if($tickets->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $tickets->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
