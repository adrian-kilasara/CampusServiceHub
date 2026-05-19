@extends('layouts.app')
@section('title', $ticket->subject)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('student.tickets.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Support Tickets</a>
        <div class="flex items-start justify-between mt-2">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $ticket->subject }}</h1>
                <p class="text-sm text-gray-400 font-mono mt-1">{{ $ticket->ticket_number }}</p>
            </div>
            @php
                $sc = ['open'=>'green','in_progress'=>'blue','resolved'=>'gray','closed'=>'gray'][$ticket->status] ?? 'gray';
                $pc = ['low'=>'gray','medium'=>'blue','high'=>'orange','urgent'=>'red'][$ticket->priority] ?? 'gray';
            @endphp
            <div class="flex gap-2 flex-shrink-0">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-{{ $pc }}-100 dark:bg-{{ $pc }}-900/30 text-{{ $pc }}-700 dark:text-{{ $pc }}-400 capitalize">
                    {{ $ticket->priority }}
                </span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-{{ $sc }}-100 dark:bg-{{ $sc }}-900/30 text-{{ $sc }}-700 dark:text-{{ $sc }}-400 capitalize">
                    {{ str_replace('_', ' ', $ticket->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Thread --}}
    <div class="space-y-4 mb-6">
        {{-- Original message --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }} <span class="text-xs font-normal text-indigo-500">(You)</span></p>
                    <p class="text-xs text-gray-400">{{ $ticket->created_at->format('M j, Y g:i A') }}</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $ticket->description }}</p>
        </div>

        {{-- Replies --}}
        @foreach($ticket->replies as $reply)
        <div class="{{ $reply->is_admin_reply ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-200 dark:border-indigo-800' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800' }} border rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 {{ $reply->is_admin_reply ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700' }} rounded-full flex items-center justify-center font-bold text-xs {{ $reply->is_admin_reply ? 'text-white' : 'text-gray-600 dark:text-gray-400' }}">
                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $reply->user->name }}
                        @if($reply->is_admin_reply)
                            <span class="text-xs font-medium bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded-full ml-1">Support</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-400">{{ $reply->created_at->format('M j, Y g:i A') }}</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $reply->message }}</p>
        </div>
        @endforeach
    </div>

    {{-- Resolved notice --}}
    @if(in_array($ticket->status, ['resolved', 'closed']))
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-4 mb-6 text-center">
        <p class="text-sm text-green-800 dark:text-green-300 font-medium">✅ This ticket has been {{ $ticket->status }}.</p>
    </div>
    @else
    {{-- Reply prompt --}}
    <div class="bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl p-4 text-center text-sm text-gray-500 dark:text-gray-400">
        <p>Waiting for support team response. We typically reply within a few hours.</p>
    </div>
    @endif
</div>
@endsection
