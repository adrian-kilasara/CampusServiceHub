@extends('layouts.app')
@section('title', 'My Requests')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Requests</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Track all your service requests.</p>
    </div>
    <a href="{{ route('student.requests.create') }}"
        class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-indigo-700 transition shadow text-sm">
        + New Request
    </a>
</div>

<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden">
    @if($requests->isEmpty())
        <div class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">
            <p class="text-5xl mb-4">📭</p>
            <p class="font-medium text-gray-700 dark:text-gray-300 mb-1">No requests yet</p>
            <p class="text-sm mb-6">Submit your first service request to get started.</p>
            <a href="{{ route('student.requests.create') }}"
                class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-indigo-700 transition text-sm">
                Create Request
            </a>
        </div>
    @else
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-3 text-left">Request</th>
                    <th class="px-6 py-3 text-left hidden md:table-cell">Service</th>
                    <th class="px-6 py-3 text-left hidden lg:table-cell">Urgency</th>
                    <th class="px-6 py-3 text-left hidden md:table-cell">Date</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @php
                    $statusColors = [
                        'pending'     => 'amber',
                        'accepted'    => 'blue',
                        'in_progress' => 'blue',
                        'completed'   => 'green',
                        'cancelled'   => 'gray',
                        'disputed'    => 'red',
                    ];
                    $urgencyColors = [
                        'low'    => 'gray',
                        'medium' => 'blue',
                        'high'   => 'orange',
                        'urgent' => 'red',
                    ];
                @endphp
                @foreach($requests as $req)
                @php
                    $sc = $statusColors[$req->status] ?? 'gray';
                    $uc = $urgencyColors[$req->urgency] ?? 'gray';
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $req->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $req->request_number }}</p>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell text-gray-600 dark:text-gray-400">
                        {{ $req->service->name }}
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-{{ $uc }}-100 dark:bg-{{ $uc }}-900/30 text-{{ $uc }}-700 dark:text-{{ $uc }}-400 capitalize">
                            {{ $req->urgency }}
                        </span>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell text-gray-500 dark:text-gray-400">
                        {{ $req->created_at->format('M j, Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-{{ $sc }}-100 dark:bg-{{ $sc }}-900/30 text-{{ $sc }}-700 dark:text-{{ $sc }}-400 capitalize">
                            {{ str_replace('_', ' ', $req->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('student.requests.show', $req) }}"
                            class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm font-medium">
                            View →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $requests->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
