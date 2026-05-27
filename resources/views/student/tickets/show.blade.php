@extends('layouts.app')
@section('title', $ticket->subject)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('student.tickets.index') }}"
           class="text-sm font-bold text-campus-pink dark:text-campus-yellow hover:underline">← Support Tickets</a>
        <div class="flex items-start justify-between mt-2 gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white">{{ $ticket->subject }}</h1>
                <p class="text-xs text-gray-400 font-mono mt-1">{{ $ticket->ticket_number }}</p>
            </div>
            @php
                $smap = ['open'=>['#d1fae5','#6ee7b7','#065f46'],'in_progress'=>['#b2e1eb','#8acfd1','#0e7490'],'resolved'=>['#f3f4f6','#d1d5db','#6b7280'],'closed'=>['#f3f4f6','#d1d5db','#6b7280']];
                $pmap = ['low'=>['#f3f4f6','#d1d5db','#6b7280'],'medium'=>['#b2e1eb','#8acfd1','#0e7490'],'high'=>['#fdedc9','#f5c96b','#b8860b'],'urgent'=>['#fee2e2','#fca5a5','#991b1b']];
                [$sbg,$sborder,$stext] = $smap[$ticket->status] ?? $smap['closed'];
                [$pbg,$pborder,$ptext] = $pmap[$ticket->priority] ?? $pmap['low'];
            @endphp
            <div class="flex gap-2 flex-shrink-0">
                <span class="text-xs font-bold px-2.5 py-1 rounded-full border capitalize"
                      style="background:{{ $pbg }};border-color:{{ $pborder }};color:{{ $ptext }}">
                    {{ $ticket->priority }}
                </span>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full border capitalize"
                      style="background:{{ $sbg }};border-color:{{ $sborder }};color:{{ $stext }}">
                    {{ str_replace('_', ' ', $ticket->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Thread --}}
    <div class="space-y-4 mb-6">
        {{-- Original message --}}
        <div class="campus-card p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-sm flex-shrink-0"
                     style="background:#f8d8ea;color:#d04f99;border:2px solid #d04f99">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-black text-gray-900 dark:text-white">
                        {{ auth()->user()->name }}
                        <span class="text-xs font-normal text-campus-pink">(You)</span>
                    </p>
                    <p class="text-xs text-gray-400">{{ $ticket->created_at->format('M j, Y g:i A') }}</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $ticket->description }}</p>
        </div>

        {{-- Replies --}}
        @foreach($ticket->replies as $reply)
        @php $isAdmin = $reply->is_admin_reply; @endphp
        <div class="rounded-2xl border-2 p-6"
             style="{{ $isAdmin ? 'background:#b2e1eb20;border-color:#8acfd1' : 'background:white;border-color:#e5e7eb' }}">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-sm flex-shrink-0 border-2"
                     style="{{ $isAdmin ? 'background:#8acfd1;color:#0e7490;border-color:#8acfd1' : 'background:#f3f4f6;color:#6b7280;border-color:#d1d5db' }}">
                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-black text-gray-900 dark:text-white">
                        {{ $reply->user->name }}
                        @if($isAdmin)
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full ml-1"
                                  style="background:#b2e1eb;color:#0e7490">Support</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-400">{{ $reply->created_at->format('M j, Y g:i A') }}</p>
                </div>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $reply->message }}</p>
        </div>
        @endforeach
    </div>

    {{-- Resolved notice or waiting notice --}}
    @if(in_array($ticket->status, ['resolved', 'closed']))
    <div class="rounded-2xl border-2 p-4 mb-6 text-center"
         style="background:#d1fae5;border-color:#6ee7b7">
        <p class="text-sm font-bold" style="color:#065f46">✅ This ticket has been {{ $ticket->status }}. Thank you!</p>
    </div>
    @else
    <div class="rounded-2xl border-2 border-dashed p-4 text-center"
         style="border-color:#8acfd1;background:#b2e1eb10">
        <p class="text-sm font-medium" style="color:#0e7490">
            ⏳ Waiting for a support team response. We typically reply within a few hours.
        </p>
    </div>
    @endif
</div>
@endsection
