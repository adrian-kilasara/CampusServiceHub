@extends('layouts.app')
@section('title', $request->title)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-start justify-between">
        <div>
            <a href="{{ route('student.requests.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← My Requests</a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $request->title }}</h1>
            <p class="text-sm text-gray-400 font-mono mt-1">{{ $request->request_number }}</p>
        </div>
        @if($request->isPending())
        <form method="POST" action="{{ route('student.requests.cancel', $request) }}"
              onsubmit="return confirm('Cancel this request?')">
            @csrf @method('PATCH')
            <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">Cancel Request</button>
        </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Status Timeline --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-5">Status Timeline</h2>
                @php
                    $steps = [
                        ['pending',     'Submitted',   'Request received and awaiting a provider.'],
                        ['accepted',    'Accepted',    'A provider has accepted your request.'],
                        ['in_progress', 'In Progress', 'Your request is being worked on.'],
                        ['completed',   'Completed',   'Service completed successfully.'],
                    ];
                    $statusOrder = ['pending' => 0, 'accepted' => 1, 'in_progress' => 2, 'completed' => 3];
                    $currentIndex = $statusOrder[$request->status] ?? -1;
                    $isCancelledOrDisputed = in_array($request->status, ['cancelled', 'disputed']);
                @endphp

                @if($isCancelledOrDisputed)
                <div class="flex items-center gap-3 p-4 rounded-xl {{ $request->status === 'cancelled' ? 'bg-gray-100 dark:bg-gray-800' : 'bg-red-50 dark:bg-red-900/20' }}">
                    <span class="text-2xl">{{ $request->status === 'cancelled' ? '❌' : '⚠️' }}</span>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $request->status) }}</p>
                        @if($request->cancellation_reason)
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $request->cancellation_reason }}</p>
                        @endif
                    </div>
                </div>
                @else
                <ol class="relative border-l border-gray-200 dark:border-gray-700 ml-3 space-y-6">
                    @foreach($steps as $i => [$key, $label, $desc])
                    @php $done = $i <= $currentIndex; @endphp
                    <li class="ml-6">
                        <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -left-3 ring-2 ring-white dark:ring-gray-900 {{ $done ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                            @if($done)
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                </svg>
                            @endif
                        </span>
                        <h3 class="font-semibold text-sm {{ $done ? 'text-gray-900 dark:text-white' : 'text-gray-400' }}">{{ $label }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $desc }}</p>
                    </li>
                    @endforeach
                </ol>
                @endif
            </div>

            {{-- Description --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-3">Description</h2>
                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed whitespace-pre-wrap">{{ $request->description }}</p>
            </div>

            {{-- Attachments --}}
            @if($request->files && count($request->files))
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-3">Attachments</h2>
                <div class="space-y-2">
                    @foreach($request->files as $file)
                    <a href="{{ Storage::url($file) }}" target="_blank"
                        class="flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                        📎 {{ basename($file) }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Provider notes --}}
            @if($request->notes)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-6">
                <h2 class="font-semibold text-blue-900 dark:text-blue-200 mb-2">Provider Notes</h2>
                <p class="text-sm text-blue-800 dark:text-blue-300">{{ $request->notes }}</p>
            </div>
            @endif

            {{-- Review form --}}
            @if($request->isCompleted() && !$request->review)
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6" x-data="{ rating: 0, hover: 0 }">
                <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Leave a Review</h2>
                <form method="POST" action="{{ route('student.requests.review', $request) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating</label>
                        <div class="flex gap-1">
                            @for($star = 1; $star <= 5; $star++)
                            <button type="button" @click="rating = {{ $star }}"
                                @mouseenter="hover = {{ $star }}" @mouseleave="hover = 0"
                                :class="(hover || rating) >= {{ $star }} ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
                                class="text-3xl transition-colors focus:outline-none">★</button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                        @error('rating') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Comment <span class="text-gray-400 font-normal">(optional)</span></label>
                        <textarea name="comment" rows="3" placeholder="How was the service?"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none text-sm">{{ old('comment') }}</textarea>
                    </div>
                    <button type="submit" :disabled="rating === 0"
                        class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                        Submit Review
                    </button>
                </form>
            </div>
            @endif

            {{-- Existing review --}}
            @if($request->review)
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-6">
                <h2 class="font-semibold text-green-900 dark:text-green-200 mb-2">Your Review</h2>
                <div class="flex items-center gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= $request->review->rating ? 'text-yellow-400' : 'text-gray-300' }} text-xl">★</span>
                    @endfor
                </div>
                @if($request->review->comment)
                    <p class="text-sm text-green-800 dark:text-green-300">{{ $request->review->comment }}</p>
                @endif
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            {{-- Details card --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 space-y-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Service</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $request->service->name }}</p>
                    <p class="text-gray-400 text-xs">{{ $request->service->category->name }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Urgency</p>
                    @php $uc = ['low'=>'gray','medium'=>'blue','high'=>'orange','urgent'=>'red'][$request->urgency]; @endphp
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-{{ $uc }}-100 dark:bg-{{ $uc }}-900/30 text-{{ $uc }}-700 dark:text-{{ $uc }}-400 capitalize">
                        {{ $request->urgency }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Submitted</p>
                    <p class="text-gray-700 dark:text-gray-300">{{ $request->created_at->format('M j, Y g:i A') }}</p>
                </div>
                @if($request->completed_at)
                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Completed</p>
                    <p class="text-gray-700 dark:text-gray-300">{{ $request->completed_at->format('M j, Y g:i A') }}</p>
                </div>
                @endif
            </div>

            {{-- Provider card --}}
            @if($request->provider)
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5">
                <p class="text-gray-400 text-xs uppercase tracking-wide mb-3">Your Provider</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-sm">
                        {{ strtoupper(substr($request->provider->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-gray-900 dark:text-white">{{ $request->provider->business_name }}</p>
                        <div class="flex items-center gap-1 text-xs text-gray-500">
                            <span class="text-yellow-400">★</span>
                            {{ number_format($request->provider->rating_avg, 1) }}
                        </div>
                    </div>
                </div>
                @if($request->provider->user->phone)
                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs text-gray-400">📞 {{ $request->provider->user->phone }}</p>
                </div>
                @endif
            </div>
            @endif

            {{-- Payment card --}}
            @if($request->payment)
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 text-sm">
                <p class="text-gray-400 text-xs uppercase tracking-wide mb-3">Payment</p>
                <div class="flex items-center justify-between">
                    <span class="text-gray-700 dark:text-gray-300">Amount</span>
                    <span class="font-bold text-gray-900 dark:text-white">₵{{ number_format($request->payment->amount, 2) }}</span>
                </div>
                @php $pc = ['paid'=>'green','pending'=>'amber','failed'=>'red','refunded'=>'blue'][$request->payment->status]; @endphp
                <div class="flex items-center justify-between mt-2">
                    <span class="text-gray-700 dark:text-gray-300">Status</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-{{ $pc }}-100 dark:bg-{{ $pc }}-900/30 text-{{ $pc }}-700 dark:text-{{ $pc }}-400 capitalize">
                        {{ $request->payment->status }}
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
