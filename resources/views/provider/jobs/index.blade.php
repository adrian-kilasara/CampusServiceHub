@extends('layouts.app')
@section('title', 'My Jobs')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white">My Jobs 🔧</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">All jobs assigned to you.</p>
    </div>
    <a href="{{ route('provider.dashboard') }}"
        class="text-sm font-bold text-campus-pink dark:text-campus-yellow hover:underline">← Dashboard</a>
</div>

<div class="campus-card overflow-hidden">
    @if($jobs->isEmpty())
        <div class="px-6 py-16 text-center">
            <p class="text-5xl mb-4">📋</p>
            <p class="font-bold text-gray-700 dark:text-gray-300 mb-1">No jobs yet</p>
            <p class="text-sm text-gray-400">Accept jobs from the dashboard to see them here.</p>
        </div>
    @else
        @php
            $smap = [
                'pending'     => ['#fdedc9','#f5c96b','#b8860b'],
                'accepted'    => ['#b2e1eb','#8acfd1','#0e7490'],
                'in_progress' => ['#b2e1eb','#8acfd1','#0e7490'],
                'completed'   => ['#d1fae5','#6ee7b7','#065f46'],
                'cancelled'   => ['#f3f4f6','#d1d5db','#6b7280'],
                'disputed'    => ['#fee2e2','#fca5a5','#991b1b'],
            ];
            $umap = [
                'low'    => ['#f3f4f6','#d1d5db','#6b7280'],
                'medium' => ['#b2e1eb','#8acfd1','#0e7490'],
                'high'   => ['#fdedc9','#f5c96b','#b8860b'],
                'urgent' => ['#fee2e2','#fca5a5','#991b1b'],
            ];
        @endphp
        <div class="divide-y divide-campus-pink/10 dark:divide-campus-dark-m">
            @foreach($jobs as $job)
            @php
                [$sbg,$sborder,$scolor] = $smap[$job->status] ?? $smap['cancelled'];
                [$ubg,$uborder,$ucolor] = $umap[$job->urgency] ?? $umap['low'];
            @endphp
            <div class="px-6 py-5 hover:bg-campus-pink-l/20 dark:hover:bg-campus-dark-m/40 transition">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-2">
                            <p class="font-black text-gray-900 dark:text-white">{{ $job->title }}</p>
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full border capitalize"
                                  style="background:{{ $sbg }};border-color:{{ $sborder }};color:{{ $scolor }}">
                                {{ str_replace('_', ' ', $job->status) }}
                            </span>
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full border capitalize"
                                  style="background:{{ $ubg }};border-color:{{ $uborder }};color:{{ $ucolor }}">
                                {{ $job->urgency }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $job->service->name }} • Requested by
                            <span class="font-bold text-gray-700 dark:text-gray-300">{{ $job->student->name }}</span>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">{{ $job->created_at->format('M j, Y g:i A') }}</p>
                        @if($job->final_price)
                            <p class="text-sm font-black mt-1" style="color:#065f46">₵{{ number_format($job->final_price, 2) }}</p>
                        @elseif($job->quoted_price)
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Quoted: ₵{{ number_format($job->quoted_price, 2) }}</p>
                        @endif
                    </div>

                    @if($provider->isApproved())
                    <div class="flex flex-col gap-2 flex-shrink-0">
                        @if($job->status === 'accepted')
                            <form method="POST" action="{{ route('provider.jobs.start', $job) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="text-xs font-bold px-4 py-2 rounded-xl border-2 transition-all hover:-translate-y-0.5 w-full"
                                    style="background:#b2e1eb;border-color:#8acfd1;color:#0e7490;box-shadow:3px 3px 0 #8acfd180">
                                    ▶ Start Job
                                </button>
                            </form>
                            <form method="POST" action="{{ route('provider.jobs.decline', $job) }}"
                                  onsubmit="return confirm('Decline this job?')">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="text-xs font-bold px-4 py-2 rounded-xl border-2 transition-all w-full"
                                    style="background:#fee2e2;border-color:#fca5a5;color:#991b1b">
                                    ✕ Decline
                                </button>
                            </form>
                        @elseif($job->status === 'in_progress')
                            <form method="POST" action="{{ route('provider.jobs.complete', $job) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="text-xs font-bold px-4 py-2 rounded-xl border-2 transition-all hover:-translate-y-0.5 w-full"
                                    style="background:#d1fae5;border-color:#6ee7b7;color:#065f46;box-shadow:3px 3px 0 #6ee7b780">
                                    ✅ Mark Complete
                                </button>
                            </form>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @if($jobs->hasPages())
        <div class="px-6 py-4 border-t border-campus-pink/10 dark:border-campus-dark-m">
            {{ $jobs->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
