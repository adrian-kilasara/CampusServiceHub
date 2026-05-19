@extends('layouts.app')
@section('title', 'Provider Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome back, {{ $provider->business_name }} 👋</h1>
    <p class="text-gray-500 dark:text-gray-400 mt-1">
        @if($provider->isApproved())
            Your account is active. Here's your job overview.
        @elseif($provider->isPending())
            <span class="text-amber-600 dark:text-amber-400 font-medium">⏳ Your account is under review.</span> You'll be notified once approved.
        @else
            <span class="text-red-600 dark:text-red-400 font-medium">❌ Your account is not active.</span> Contact support for assistance.
        @endif
    </p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['Completed Jobs', $stats['completed'], 'bg-green-50 dark:bg-green-900/30', 'text-green-600 dark:text-green-400'],
        ['Active Jobs', $stats['active'], 'bg-blue-50 dark:bg-blue-900/30', 'text-blue-600 dark:text-blue-400'],
        ['Rating', number_format($stats['rating'], 1) . ' / 5', 'bg-yellow-50 dark:bg-yellow-900/30', 'text-yellow-600 dark:text-yellow-400'],
        ['Earnings', '₵' . number_format($stats['earnings'], 2), 'bg-indigo-50 dark:bg-indigo-900/30', 'text-indigo-600 dark:text-indigo-400'],
    ] as [$label, $value, $bg, $color])
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5">
        <div class="{{ $bg }} rounded-lg px-2 py-1 w-fit mb-3">
            <span class="{{ $color }} text-xs font-semibold">{{ $label }}</span>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $value }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Active Jobs --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
            <h2 class="font-semibold text-gray-900 dark:text-white">Active Jobs</h2>
            <a href="{{ route('provider.jobs.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View all</a>
        </div>

        @if($activeJobs->isEmpty())
            <div class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                <p class="text-3xl mb-2">🎯</p>
                <p class="text-sm">No active jobs right now.</p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($activeJobs as $job)
                @php $color = $job->status === 'in_progress' ? 'blue' : 'amber'; @endphp
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $job->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $job->service->name }} • {{ $job->student->name }}</p>
                            <p class="text-xs text-gray-400">{{ $job->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex flex-col gap-2 flex-shrink-0">
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-{{ $color }}-100 dark:bg-{{ $color }}-900/30 text-{{ $color }}-700 dark:text-{{ $color }}-400 capitalize text-center">
                                {{ str_replace('_', ' ', $job->status) }}
                            </span>
                            @if($provider->isApproved())
                                @if($job->status === 'accepted')
                                <form method="POST" action="{{ route('provider.jobs.start', $job) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs w-full bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 transition">
                                        Start
                                    </button>
                                </form>
                                @elseif($job->status === 'in_progress')
                                <form method="POST" action="{{ route('provider.jobs.complete', $job) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs w-full bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700 transition">
                                        Complete
                                    </button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Available Jobs --}}
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="font-semibold text-gray-900 dark:text-white">Available Jobs</h2>
            <p class="text-xs text-gray-400 mt-0.5">Pending requests matching your services</p>
        </div>

        @if(!$provider->isApproved())
            <div class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                <p class="text-3xl mb-2">🔒</p>
                <p class="text-sm">Available once your account is approved.</p>
            </div>
        @elseif($pendingJobs->isEmpty())
            <div class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                <p class="text-3xl mb-2">✅</p>
                <p class="text-sm">No pending jobs right now. Check back soon!</p>
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($pendingJobs as $job)
                @php $uc = ['low'=>'gray','medium'=>'blue','high'=>'orange','urgent'=>'red'][$job->urgency]; @endphp
                <div class="px-6 py-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $job->title }}</p>
                                <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full bg-{{ $uc }}-100 dark:bg-{{ $uc }}-900/30 text-{{ $uc }}-700 dark:text-{{ $uc }}-400 capitalize flex-shrink-0">
                                    {{ $job->urgency }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400">{{ $job->service->name }} • {{ $job->created_at->diffForHumans() }}</p>
                            @if($job->service->base_price)
                                <p class="text-xs text-green-600 dark:text-green-400 font-medium mt-1">from ₵{{ number_format($job->service->base_price, 2) }}</p>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('provider.jobs.accept', $job) }}" class="flex-shrink-0">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="text-xs bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">
                                Accept
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
