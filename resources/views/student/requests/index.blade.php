@extends('layouts.app')
@section('title', 'My Requests')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white">My Requests 📋</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Track all your service requests.</p>
    </div>
    <a href="{{ route('student.requests.create') }}" class="campus-btn">+ New Request</a>
</div>

<div class="campus-card overflow-hidden">
    @if($requests->isEmpty())
        <div class="px-6 py-16 text-center">
            <p class="text-5xl mb-4">📭</p>
            <p class="font-bold text-gray-700 dark:text-gray-300 mb-1">No requests yet</p>
            <p class="text-sm text-gray-400 mb-6">Submit your first service request to get started.</p>
            <a href="{{ route('student.requests.create') }}" class="campus-btn">Create Request</a>
        </div>
    @else
        @php
            $smap = ['pending'=>['#fdedc9','#f5c96b','#b8860b'],'accepted'=>['#b2e1eb','#8acfd1','#0e7490'],'in_progress'=>['#b2e1eb','#8acfd1','#0e7490'],'completed'=>['#d1fae5','#6ee7b7','#065f46'],'cancelled'=>['#f3f4f6','#d1d5db','#6b7280'],'disputed'=>['#fee2e2','#fca5a5','#991b1b']];
            $umap = ['low'=>['#f3f4f6','#d1d5db','#6b7280'],'medium'=>['#b2e1eb','#8acfd1','#0e7490'],'high'=>['#fdedc9','#f5c96b','#b8860b'],'urgent'=>['#fee2e2','#fca5a5','#991b1b']];
        @endphp
        <table class="w-full text-sm">
            <thead style="background:linear-gradient(90deg,rgba(208,79,153,0.07),rgba(138,207,209,0.07))">
                <tr class="text-xs font-black uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    <th class="px-6 py-3 text-left">Request</th>
                    <th class="px-6 py-3 text-left hidden md:table-cell">Service</th>
                    <th class="px-6 py-3 text-left hidden lg:table-cell">Urgency</th>
                    <th class="px-6 py-3 text-left hidden md:table-cell">Date</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-campus-pink/10 dark:divide-campus-dark-m">
                @foreach($requests as $req)
                @php
                    [$sbg,$sborder,$scolor] = $smap[$req->status] ?? $smap['cancelled'];
                    [$ubg,$uborder,$ucolor] = $umap[$req->urgency] ?? $umap['low'];
                @endphp
                <tr class="hover:bg-campus-pink-l/30 dark:hover:bg-campus-dark-m/50 transition">
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900 dark:text-white">{{ $req->title }}</p>
                        <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $req->request_number }}</p>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell text-gray-600 dark:text-gray-400 font-medium">{{ $req->service->name }}</td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full border capitalize"
                              style="background:{{ $ubg }};border-color:{{ $uborder }};color:{{ $ucolor }}">
                            {{ $req->urgency }}
                        </span>
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell text-gray-500 dark:text-gray-400 text-xs font-medium">{{ $req->created_at->format('M j, Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full border capitalize"
                              style="background:{{ $sbg }};border-color:{{ $sborder }};color:{{ $scolor }}">
                            {{ str_replace('_', ' ', $req->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('student.requests.show', $req) }}"
                            class="text-campus-pink dark:text-campus-yellow hover:underline text-sm font-bold">View →</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-campus-pink/10 dark:border-campus-dark-m">
            {{ $requests->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
