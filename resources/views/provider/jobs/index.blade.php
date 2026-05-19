@extends('layouts.app')
@section('title', 'My Jobs')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Jobs</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">All jobs assigned to you.</p>
    </div>
    <a href="{{ route('provider.dashboard') }}"
        class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Dashboard</a>
</div>

<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
    @if($jobs->isEmpty())
        <div class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">
            <p class="text-5xl mb-4">📋</p>
            <p class="font-medium text-gray-700 dark:text-gray-300 mb-1">No jobs yet</p>
            <p class="text-sm">Accept jobs from the dashboard to see them here.</p>
        </div>
    @else
        @php
            $statusColors = [
                'pending'     => 'amber',
                'accepted'    => 'blue',
                'in_progress' => 'blue',
                'completed'   => 'green',
                'cancelled'   => 'gray',
                'disputed'    => 'red',
            ];
            $urgencyColors = ['low'=>'gray','medium'=>'blue','high'=>'orange','urgent'=>'red'];
        @endphp
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($jobs as $job)
            @php
                $sc = $statusColors[$job->status] ?? 'gray';
                $uc = $urgencyColors[$job->urgency] ?? 'gray';
            @endphp
            <div class="px-6 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $job->title }}</p>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-{{ $sc }}-100 dark:bg-{{ $sc }}-900/30 text-{{ $sc }}-700 dark:text-{{ $sc }}-400 capitalize">
                                {{ str_replace('_', ' ', $job->status) }}
                            </span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-{{ $uc }}-100 dark:bg-{{ $uc }}-900/30 text-{{ $uc }}-700 dark:text-{{ $uc }}-400 capitalize">
                                {{ $job->urgency }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $job->service->name }} • Requested by <span class="font-medium text-gray-700 dark:text-gray-300">{{ $job->student->name }}</span></p>
                        <p class="text-xs text-gray-400 mt-1">{{ $job->created_at->format('M j, Y g:i A') }}</p>
                        @if($job->final_price)
                            <p class="text-sm font-semibold text-green-600 dark:text-green-400 mt-1">₵{{ number_format($job->final_price, 2) }}</p>
                        @elseif($job->quoted_price)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Quoted: ₵{{ number_format($job->quoted_price, 2) }}</p>
                        @endif
                    </div>

                    @if($provider->isApproved())
                    <div class="flex flex-col gap-2 flex-shrink-0">
                        @if($job->status === 'accepted')
                            <form method="POST" action="{{ route('provider.jobs.start', $job) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-sm w-full bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition font-semibold">
                                    Start Job
                                </button>
                            </form>
                            <form method="POST" action="{{ route('provider.jobs.decline', $job) }}"
                                  onsubmit="return confirm('Decline this job?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-sm w-full text-red-600 dark:text-red-400 border border-red-300 dark:border-red-700 px-4 py-2 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                    Decline
                                </button>
                            </form>
                        @elseif($job->status === 'in_progress')
                            <form method="POST" action="{{ route('provider.jobs.complete', $job) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-sm w-full bg-green-600 text-white px-4 py-2 rounded-xl hover:bg-green-700 transition font-semibold">
                                    Mark Complete
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
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $jobs->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
