@extends('layouts.app')
@section('title', 'Open Support Ticket')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('student.tickets.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Back to tickets</a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Open a Support Ticket</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Describe your issue and our team will respond promptly.</p>
    </div>

    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-8">
        <form method="POST" action="{{ route('student.tickets.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                    placeholder="Brief description of your issue"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('subject') border-red-500 @enderror">
                @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Priority</label>
                <div class="grid grid-cols-4 gap-3">
                    @foreach([
                        ['low', '💬', 'Low', 'General question'],
                        ['medium', '📋', 'Medium', 'Issue affecting me'],
                        ['high', '⚠️', 'High', 'Urgent problem'],
                        ['urgent', '🚨', 'Urgent', 'Critical, blocking me'],
                    ] as [$val, $icon, $label, $hint])
                    <label class="cursor-pointer">
                        <input type="radio" name="priority" value="{{ $val }}" class="sr-only peer"
                            {{ old('priority', 'medium') === $val ? 'checked' : '' }}>
                        <div class="border-2 border-gray-200 dark:border-gray-700 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 rounded-xl p-3 text-center transition hover:border-indigo-300">
                            <span class="text-xl block mb-1">{{ $icon }}</span>
                            <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $label }}</p>
                            <p class="text-xs text-gray-400 mt-0.5 leading-tight">{{ $hint }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('priority') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea name="description" rows="6" required
                    placeholder="Please describe your issue in detail. Include any relevant request numbers, dates, or steps to reproduce."
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-semibold hover:bg-indigo-700 transition">
                    Submit Ticket
                </button>
                <a href="{{ route('student.tickets.index') }}"
                    class="px-6 py-3 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
