@extends('layouts.app')
@section('title', $request->title)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('student.requests.index') }}"
               class="text-sm font-bold text-campus-pink dark:text-campus-yellow hover:underline">← My Requests</a>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white mt-2">{{ $request->title }}</h1>
            <p class="text-xs text-gray-400 font-mono mt-1">{{ $request->request_number }}</p>
        </div>
        @if($request->isPending())
        <form method="POST" action="{{ route('student.requests.cancel', $request) }}"
              onsubmit="return confirm('Cancel this request?')">
            @csrf @method('PATCH')
            <button type="submit"
                class="text-sm font-bold px-4 py-2 rounded-xl border-2 transition-all"
                style="background:#fee2e2;border-color:#fca5a5;color:#991b1b">
                ✕ Cancel Request
            </button>
        </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Status Timeline --}}
            <div class="campus-card p-6">
                <h2 class="font-black text-gray-900 dark:text-white mb-5">📍 Status Timeline</h2>
                @php
                    $steps = [
                        ['pending',     'Submitted',   'Request received and awaiting a provider.'],
                        ['accepted',    'Accepted',    'A provider has accepted your request.'],
                        ['in_progress', 'In Progress', 'Your request is being worked on.'],
                        ['completed',   'Completed',   'Service completed successfully.'],
                    ];
                    $statusOrder = ['pending'=>0,'accepted'=>1,'in_progress'=>2,'completed'=>3];
                    $currentIndex = $statusOrder[$request->status] ?? -1;
                    $isCancelledOrDisputed = in_array($request->status, ['cancelled','disputed']);
                @endphp

                @if($isCancelledOrDisputed)
                <div class="flex items-center gap-3 p-4 rounded-2xl border-2"
                     style="{{ $request->status === 'cancelled' ? 'background:#f3f4f6;border-color:#d1d5db;color:#6b7280' : 'background:#fee2e2;border-color:#fca5a5;color:#991b1b' }}">
                    <span class="text-2xl">{{ $request->status === 'cancelled' ? '❌' : '⚠️' }}</span>
                    <div>
                        <p class="font-black capitalize">{{ str_replace('_', ' ', $request->status) }}</p>
                        @if($request->cancellation_reason)
                            <p class="text-sm opacity-80 mt-0.5">{{ $request->cancellation_reason }}</p>
                        @endif
                    </div>
                </div>
                @else
                <ol class="relative border-l-2 ml-3 space-y-6" style="border-color:#d04f99">
                    @foreach($steps as $i => [$key, $label, $desc])
                    @php $done = $i <= $currentIndex; @endphp
                    <li class="ml-6">
                        <span class="absolute flex items-center justify-center w-7 h-7 rounded-full -left-3.5 border-2 border-white dark:border-campus-dark-c font-black text-xs"
                              style="{{ $done ? 'background:#d04f99;color:#fff' : 'background:#f3f4f6;color:#9ca3af' }}">
                            @if($done) ✓ @else {{ $i + 1 }} @endif
                        </span>
                        <h3 class="font-black text-sm {{ $done ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">{{ $label }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $desc }}</p>
                    </li>
                    @endforeach
                </ol>
                @endif
            </div>

            {{-- Description --}}
            <div class="campus-card p-6">
                <h2 class="font-black text-gray-900 dark:text-white mb-3">📝 Description</h2>
                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $request->description }}</p>
            </div>

            {{-- Attachments --}}
            @if($request->files && count($request->files))
            <div class="campus-card p-6">
                <h2 class="font-black text-gray-900 dark:text-white mb-3">📎 Attachments</h2>
                <div class="space-y-2">
                    @foreach($request->files as $file)
                    <a href="{{ Storage::url($file) }}" target="_blank"
                        class="flex items-center gap-2 text-sm font-bold text-campus-pink dark:text-campus-yellow hover:underline">
                        📎 {{ basename($file) }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Provider notes --}}
            @if($request->notes)
            <div class="rounded-2xl border-2 p-6" style="background:#b2e1eb20;border-color:#8acfd1">
                <h2 class="font-black mb-2" style="color:#0e7490">💬 Provider Notes</h2>
                <p class="text-sm" style="color:#0e7490">{{ $request->notes }}</p>
            </div>
            @endif

            {{-- Review form --}}
            @if($request->isCompleted() && !$request->review)
            <div class="campus-card p-6" x-data="{ rating: 0, hover: 0 }">
                <h2 class="font-black text-gray-900 dark:text-white mb-4">⭐ Leave a Review</h2>
                <form method="POST" action="{{ route('student.requests.review', $request) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Rating</label>
                        <div class="flex gap-1">
                            @for($star = 1; $star <= 5; $star++)
                            <button type="button" @click="rating = {{ $star }}"
                                @mouseenter="hover = {{ $star }}" @mouseleave="hover = 0"
                                :class="(hover || rating) >= {{ $star }} ? 'text-campus-yellow-h' : 'text-gray-300 dark:text-gray-600'"
                                class="text-3xl transition-colors focus:outline-none">★</button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                        @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Comment <span class="text-gray-400 font-normal">(optional)</span></label>
                        <textarea name="comment" rows="3" placeholder="How was the service?"
                            class="campus-input resize-none text-sm">{{ old('comment') }}</textarea>
                    </div>
                    <button type="submit" :disabled="rating === 0"
                        class="campus-btn text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Submit Review ★
                    </button>
                </form>
            </div>
            @endif

            {{-- Existing review --}}
            @if($request->review)
            <div class="rounded-2xl border-2 p-6" style="background:#d1fae5;border-color:#6ee7b7">
                <h2 class="font-black mb-2" style="color:#065f46">✅ Your Review</h2>
                <div class="flex items-center gap-0.5 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="text-xl {{ $i <= $request->review->rating ? 'text-campus-yellow-h' : 'text-gray-300' }}">★</span>
                    @endfor
                </div>
                @if($request->review->comment)
                    <p class="text-sm" style="color:#065f46">{{ $request->review->comment }}</p>
                @endif
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            {{-- Details card --}}
            <div class="campus-card p-5 space-y-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs font-black uppercase tracking-widest mb-1">Service</p>
                    <p class="font-black text-gray-900 dark:text-white">{{ $request->service->name }}</p>
                    <p class="text-gray-400 text-xs mt-0.5">{{ $request->service->category->name }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2">Urgency</p>
                    @php
                        $umap = ['low'=>['#f3f4f6','#d1d5db','#6b7280'],'medium'=>['#b2e1eb','#8acfd1','#0e7490'],'high'=>['#fdedc9','#f5c96b','#b8860b'],'urgent'=>['#fee2e2','#fca5a5','#991b1b']];
                        [$ubg,$uborder,$utext] = $umap[$request->urgency] ?? $umap['low'];
                    @endphp
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full border capitalize"
                          style="background:{{ $ubg }};border-color:{{ $uborder }};color:{{ $utext }}">
                        {{ $request->urgency }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-400 text-xs font-black uppercase tracking-widest mb-1">Submitted</p>
                    <p class="text-gray-700 dark:text-gray-300 font-medium">{{ $request->created_at->format('M j, Y g:i A') }}</p>
                </div>
                @if($request->completed_at)
                <div>
                    <p class="text-gray-400 text-xs font-black uppercase tracking-widest mb-1">Completed</p>
                    <p class="text-gray-700 dark:text-gray-300 font-medium">{{ $request->completed_at->format('M j, Y g:i A') }}</p>
                </div>
                @endif
            </div>

            {{-- Provider card --}}
            @if($request->provider)
            <div class="campus-card p-5">
                <p class="text-gray-400 text-xs font-black uppercase tracking-widest mb-3">Your Provider</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-sm"
                         style="background:#f8d8ea;color:#d04f99;border:2px solid #d04f99">
                        {{ strtoupper(substr($request->provider->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-black text-sm text-gray-900 dark:text-white">{{ $request->provider->business_name }}</p>
                        <div class="flex items-center gap-1 text-xs text-gray-500 mt-0.5">
                            <span class="text-campus-yellow-h">★</span>
                            <span class="font-medium">{{ number_format($request->provider->rating_avg, 1) }}</span>
                        </div>
                    </div>
                </div>
                @if($request->provider->user->phone)
                <div class="mt-3 pt-3 border-t-2 border-campus-pink/10 dark:border-campus-dark-m">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">📞 {{ $request->provider->user->phone }}</p>
                </div>
                @endif
            </div>
            @endif

            {{-- Payment card --}}
            @if($request->payment)
            <div class="campus-card p-5 text-sm">
                <p class="text-gray-400 text-xs font-black uppercase tracking-widest mb-3">Payment</p>
                <div class="flex items-center justify-between">
                    <span class="text-gray-700 dark:text-gray-300 font-medium">Amount</span>
                    <span class="font-black text-gray-900 dark:text-white">₵{{ number_format($request->payment->amount, 2) }}</span>
                </div>
                @php
                    $pmap = ['paid'=>['#d1fae5','#6ee7b7','#065f46'],'pending'=>['#fdedc9','#f5c96b','#b8860b'],'failed'=>['#fee2e2','#fca5a5','#991b1b'],'refunded'=>['#b2e1eb','#8acfd1','#0e7490']];
                    [$pbg,$pborder,$ptext] = $pmap[$request->payment->status] ?? $pmap['pending'];
                @endphp
                <div class="flex items-center justify-between mt-2">
                    <span class="text-gray-700 dark:text-gray-300 font-medium">Status</span>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full border capitalize"
                          style="background:{{ $pbg }};border-color:{{ $pborder }};color:{{ $ptext }}">
                        {{ $request->payment->status }}
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
