@extends('layouts.app')
@section('title', 'New Service Request')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('student.requests.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">← Back to requests</a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-2">New Service Request</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Describe what you need — a provider will respond shortly.</p>
    </div>

    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-8"
         x-data="requestForm()">
        <form method="POST" action="{{ route('student.requests.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Category → Service selector --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Service Category</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($categories as $category)
                    <button type="button"
                        @click="selectCategory({{ $category->id }}, {{ $category->services->toJson() }})"
                        :class="selectedCategory === {{ $category->id }}
                            ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300'
                            : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700'"
                        class="flex flex-col items-center p-4 border-2 rounded-xl transition text-center cursor-pointer">
                        <span class="text-2xl mb-1">
                            @php
                                $icons = ['Printing' => '🖨️', 'Tech Repair' => '💻', 'Delivery' => '🚚', 'Food' => '🍔', 'Tutoring' => '📚', 'Creative' => '🎨'];
                                echo $icons[$category->name] ?? '📦';
                            @endphp
                        </span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $category->name }}</span>
                        <span class="text-xs text-gray-400 mt-0.5">{{ $category->services->count() }} services</span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Service picker --}}
            <div x-show="services.length > 0" x-transition>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Choose a Service</label>
                <select name="service_id" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select service...</option>
                    <template x-for="svc in services" :key="svc.id">
                        <option :value="svc.id" x-text="svc.name + (svc.base_price ? ' — from ₵' + svc.base_price : '')"></option>
                    </template>
                </select>
                @error('service_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Request Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    placeholder="e.g. Print 20 pages double-sided"
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea name="description" rows="4" required
                    placeholder="Provide as much detail as possible — paper size, quantity, deadline, delivery location, etc."
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('description') border-red-500 @enderror resize-none">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Urgency --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Urgency Level</label>
                <div class="grid grid-cols-4 gap-3">
                    @foreach([
                        ['low', '🟢', 'Low', 'No rush, flexible timeline'],
                        ['medium', '🟡', 'Medium', 'Within a day or two'],
                        ['high', '🟠', 'High', 'Today if possible'],
                        ['urgent', '🔴', 'Urgent', 'ASAP, critical need'],
                    ] as [$val, $icon, $label, $hint])
                    <label class="cursor-pointer">
                        <input type="radio" name="urgency" value="{{ $val }}" class="sr-only peer" {{ old('urgency') === $val ? 'checked' : ($val === 'medium' && !old('urgency') ? 'checked' : '') }}>
                        <div class="border-2 border-gray-200 dark:border-gray-700 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 rounded-xl p-3 text-center transition hover:border-indigo-300">
                            <span class="text-2xl block mb-1">{{ $icon }}</span>
                            <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $label }}</p>
                            <p class="text-xs text-gray-400 mt-0.5 leading-tight">{{ $hint }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('urgency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- File attachments --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attachments <span class="text-gray-400 font-normal">(optional, max 10MB each)</span></label>
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-6 text-center hover:border-indigo-400 transition"
                     @dragover.prevent @drop.prevent="handleDrop($event)">
                    <input type="file" name="files[]" multiple id="file-upload" class="sr-only"
                           @change="handleFiles($event)">
                    <label for="file-upload" class="cursor-pointer">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">Click to upload</span> or drag and drop
                        </p>
                        <p class="text-xs text-gray-400 mt-1">PDF, DOCX, PNG, JPG up to 10MB</p>
                    </label>
                    <div x-show="fileNames.length > 0" class="mt-3 space-y-1">
                        <template x-for="name in fileNames" :key="name">
                            <p class="text-xs text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded-lg px-3 py-1" x-text="'📎 ' + name"></p>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-semibold hover:bg-indigo-700 transition">
                    Submit Request
                </button>
                <a href="{{ route('student.requests.index') }}"
                    class="px-6 py-3 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function requestForm() {
    return {
        selectedCategory: null,
        services: [],
        fileNames: [],
        selectCategory(id, services) {
            this.selectedCategory = id;
            this.services = services;
        },
        handleFiles(e) {
            this.fileNames = Array.from(e.target.files).map(f => f.name);
        },
        handleDrop(e) {
            const dt = new DataTransfer();
            Array.from(e.dataTransfer.files).forEach(f => dt.items.add(f));
            document.getElementById('file-upload').files = dt.files;
            this.fileNames = Array.from(e.dataTransfer.files).map(f => f.name);
        },
    }
}
</script>
@endpush
@endsection
