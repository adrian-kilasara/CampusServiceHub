@extends('layouts.app')
@section('title', 'Support Tickets')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white">Support Tickets 🎫</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Get help from our support team.</p>
    </div>
    <a href="{{ route('student.tickets.create') }}" class="campus-btn">+ New Ticket</a>
</div>

<div class="campus-card overflow-hidden">
    @if($tickets->isEmpty())
        <div class="px-6 py-16 text-center">
            <p class="text-5xl mb-4">🎫</p>
            <p class="font-bold text-gray-700 dark:text-gray-300 mb-1">No tickets yet</p>
            <p class="text-sm text-gray-400 mb-6">Need help? Open a support ticket and we'll get back to you.</p>
            <a href="{{ route('student.tickets.create') }}" class="campus-btn">Open Ticket</a>
        </div>
    @else
        @php
            $smap = [
                'open'        => ['#d1fae5','#6ee7b7','#065f46'],
                'in_progress' => ['#b2e1eb','#8acfd1','#0e7490'],
                'resolved'    => ['#f3f4f6','#d1d5db','#6b7280'],
                'closed'      => ['#f3f4f6','#d1d5db','#6b7280'],
            ];
            $pmap = [
                'low'    => ['#f3f4f6','#d1d5db','#6b7280'],
                'medium' => ['#b2e1eb','#8acfd1','#0e7490'],
                'high'   => ['#fdedc9','#f5c96b','#b8860b'],
                'urgent' => ['#fee2e2','#fca5a5','#991b1b'],
            ];
        @endphp
        <div class="divide-y divide-campus-pink/10 dark:divide-campus-dark-m">
            @foreach($tickets as $ticket)
            @php
                [$sbg,$sborder,$stext] = $smap[$ticket->status] ?? $smap['closed'];
                [$pbg,$pborder,$ptext] = $pmap[$ticket->priority] ?? $pmap['low'];
            @endphp
            <a href="{{ route('student.tickets.show', $ticket) }}"
                class="flex items-center justify-between px-6 py-4 hover:bg-campus-pink-l/30 dark:hover:bg-campus-dark-m/50 transition">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                         style="background:#fdedc9;border:2px solid #f5c96b">
                        🎫
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $ticket->subject }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $ticket->ticket_number }} • {{ $ticket->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full border capitalize hidden sm:inline-flex"
                          style="background:{{ $pbg }};border-color:{{ $pborder }};color:{{ $ptext }}">
                        {{ $ticket->priority }}
                    </span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full border capitalize"
                          style="background:{{ $sbg }};border-color:{{ $sborder }};color:{{ $stext }}">
                        {{ str_replace('_', ' ', $ticket->status) }}
                    </span>
                    <span class="text-campus-pink dark:text-campus-yellow font-bold text-sm">→</span>
                </div>
            </a>
            @endforeach
        </div>
        @if($tickets->hasPages())
        <div class="px-6 py-4 border-t border-campus-pink/10 dark:border-campus-dark-m">
            {{ $tickets->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
