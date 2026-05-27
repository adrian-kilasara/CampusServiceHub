@extends('layouts.app')
@section('title', 'Provider Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-black text-gray-900 dark:text-white">
        Welcome back, {{ $provider->business_name }}! 💼
    </h1>
    <p class="mt-1 font-medium">
        @if($provider->isApproved())
            <span class="text-campus-teal-h dark:text-campus-teal">✅ Your account is active. Here's your job overview.</span>
        @elseif($provider->isPending())
            <span class="font-bold" style="color:#b8860b">⏳ Your account is under review.</span>
            <span class="text-gray-500 dark:text-gray-400"> You'll be notified once approved.</span>
        @else
            <span class="text-red-500 font-bold">❌ Your account is not active.</span>
            <span class="text-gray-500 dark:text-gray-400"> Contact support for assistance.</span>
        @endif
    </p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['Completed Jobs', $stats['completed'],                         '#d1fae5','#6ee7b7','#065f46','✅'],
        ['Active Jobs',    $stats['active'],                            '#b2e1eb','#8acfd1','#0e7490','🔧'],
        ['Rating',         number_format($stats['rating'],1) . ' / 5', '#fdedc9','#f5c96b','#b8860b','⭐'],
        ['Earnings',       '₵'.number_format($stats['earnings'],2),    '#f8d8ea','#d04f99','#9d174d','💰'],
    ] as [$label, $value, $bg, $border, $text, $icon])
    <div class="rounded-2xl p-5 border-2 hover:-translate-y-0.5 transition-all"
         style="background:{{ $bg }};border-color:{{ $border }};box-shadow:3px 3px 0 {{ $border }}80">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xl">{{ $icon }}</span>
            <span class="text-xs font-bold" style="color:{{ $text }}">{{ $label }}</span>
        </div>
        <p class="text-3xl font-black" style="color:{{ $text }}">{{ $value }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Active Jobs --}}
    <div class="campus-card overflow-hidden">
        <div class="px-6 py-4 border-b-2 border-campus-pink/10 dark:border-campus-dark-m flex justify-between items-center"
             style="background:linear-gradient(90deg,rgba(138,207,209,0.08),rgba(208,79,153,0.05))">
            <h2 class="font-black text-gray-900 dark:text-white">🔧 Active Jobs</h2>
            <a href="{{ route('provider.jobs.index') }}" class="text-sm font-bold text-campus-pink dark:text-campus-yellow hover:underline">View all →</a>
        </div>

        @if($activeJobs->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-3xl mb-2">🎯</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">No active jobs right now.</p>
            </div>
        @else
            <div class="divide-y divide-campus-pink/10 dark:divide-campus-dark-m">
                @foreach($activeJobs as $job)
                @php $ip = $job->status === 'in_progress'; @endphp
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $job->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $job->service->name }} • {{ $job->student->name }}</p>
                            <p class="text-xs text-gray-400">{{ $job->created_at->diffForHumans() }}</p>
                        </div>
                        @if($provider->isApproved())
                        <div class="flex flex-col gap-2 flex-shrink-0">
                            @if($job->status === 'accepted')
                            <form method="POST" action="{{ route('provider.jobs.start', $job) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="text-xs font-bold px-3 py-1.5 rounded-lg border-2 transition-all"
                                    style="background:#b2e1eb;border-color:#8acfd1;color:#0e7490">
                                    ▶ Start
                                </button>
                            </form>
                            @elseif($ip)
                            <form method="POST" action="{{ route('provider.jobs.complete', $job) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="text-xs font-bold px-3 py-1.5 rounded-lg border-2 transition-all"
                                    style="background:#d1fae5;border-color:#6ee7b7;color:#065f46">
                                    ✅ Complete
                                </button>
                            </form>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Available Jobs --}}
    <div class="campus-card overflow-hidden">
        <div class="px-6 py-4 border-b-2 border-campus-pink/10 dark:border-campus-dark-m"
             style="background:linear-gradient(90deg,rgba(251,226,167,0.15),rgba(208,79,153,0.05))">
            <h2 class="font-black text-gray-900 dark:text-white">📥 Available Jobs</h2>
            <p class="text-xs text-gray-400 font-medium mt-0.5">Pending requests matching your services</p>
        </div>

        @if(!$provider->isApproved())
            <div class="px-6 py-10 text-center">
                <p class="text-3xl mb-2">🔒</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Available once your account is approved.</p>
            </div>
        @elseif($pendingJobs->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-3xl mb-2">✨</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">No pending jobs right now. Check back soon!</p>
            </div>
        @else
            <div class="divide-y divide-campus-pink/10 dark:divide-campus-dark-m">
                @php
                    $urgencyMap = ['low'=>['#f3f4f6','#d1d5db','#6b7280'],'medium'=>['#b2e1eb','#8acfd1','#0e7490'],'high'=>['#fdedc9','#f5c96b','#b8860b'],'urgent'=>['#fee2e2','#fca5a5','#991b1b']];
                @endphp
                @foreach($pendingJobs as $job)
                @php [$ubg,$uborder,$utext] = $urgencyMap[$job->urgency] ?? $urgencyMap['low']; @endphp
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                                <p class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $job->title }}</p>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full border capitalize flex-shrink-0"
                                      style="background:{{ $ubg }};border-color:{{ $uborder }};color:{{ $utext }}">
                                    {{ $job->urgency }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400">{{ $job->service->name }} • {{ $job->created_at->diffForHumans() }}</p>
                            @if($job->service->base_price)
                                <p class="text-xs font-bold mt-1" style="color:#065f46">from ₵{{ number_format($job->service->base_price, 2) }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('provider.jobs.accept', $job) }}" class="flex-shrink-0">
                            @csrf @method('PATCH')
                            <button type="submit" class="campus-btn text-xs py-2">Accept</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
