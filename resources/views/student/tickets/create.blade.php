@extends('layouts.app')
@section('title', 'Open Support Ticket')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('student.tickets.index') }}"
           class="text-sm font-bold text-campus-pink dark:text-campus-yellow hover:underline">← Back to tickets</a>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white mt-2">Open a Support Ticket 🎫</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Describe your issue and our team will respond promptly.</p>
    </div>

    <div class="campus-card p-8">
        <form method="POST" action="{{ route('student.tickets.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                    placeholder="Brief description of your issue"
                    class="campus-input @error('subject') border-red-400 @enderror">
                @error('subject') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Priority</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach([
                        ['low',    '#f3f4f6','#d1d5db','#6b7280', '💬', 'Low',    'General question'],
                        ['medium', '#b2e1eb','#8acfd1','#0e7490', '📋', 'Medium', 'Issue affecting me'],
                        ['high',   '#fdedc9','#f5c96b','#b8860b', '⚠️', 'High',   'Urgent problem'],
                        ['urgent', '#fee2e2','#fca5a5','#991b1b', '🚨', 'Urgent', 'Critical!'],
                    ] as [$val, $bg, $border, $text, $icon, $label, $hint])
                    <label class="cursor-pointer">
                        <input type="radio" name="priority" value="{{ $val }}" class="sr-only peer"
                            {{ old('priority', 'medium') === $val ? 'checked' : '' }}>
                        <div class="border-2 rounded-2xl p-3 text-center transition-all hover:-translate-y-0.5 peer-checked:scale-[0.97]"
                             style="background:{{ $bg }};border-color:{{ $border }};color:{{ $text }};box-shadow:3px 3px 0 {{ $border }}80">
                            <span class="text-xl block mb-1">{{ $icon }}</span>
                            <p class="text-xs font-black">{{ $label }}</p>
                            <p class="text-xs mt-0.5 opacity-75 leading-tight">{{ $hint }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('priority') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea name="description" rows="6" required
                    placeholder="Please describe your issue in detail. Include any relevant request numbers, dates, or steps to reproduce."
                    class="campus-input resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="campus-btn flex-1 justify-center py-3">
                    Submit Ticket →
                </button>
                <a href="{{ route('student.tickets.index') }}" class="campus-btn-outline px-6 py-3">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
