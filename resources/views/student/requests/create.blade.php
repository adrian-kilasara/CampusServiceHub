@extends('layouts.app')
@section('title', 'New Service Request')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('student.requests.index') }}"
           class="text-sm font-bold text-campus-pink dark:text-campus-yellow hover:underline">← Back to requests</a>
    </div>
    <div class="mb-6">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white">New Service Request ✍️</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium">Describe what you need — a provider will respond shortly.</p>
    </div>

    <div class="campus-card p-8" x-data="requestForm()">
        <form method="POST" action="{{ route('student.requests.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Category → Service selector --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Service Category</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($categories as $category)
                    @php
                        $catPalettes = [
                            0 => ['#fdedc9','#f5c96b','#b8860b'],
                            1 => ['#b2e1eb','#8acfd1','#0e7490'],
                            2 => ['#f8d8ea','#d04f99','#9d174d'],
                            3 => ['#d1fae5','#6ee7b7','#065f46'],
                            4 => ['#ede9fe','#a78bfa','#5b21b6'],
                            5 => ['#fee2e2','#fca5a5','#991b1b'],
                        ];
                        [$cbg,$cborder,$ctext] = $catPalettes[$loop->index % 6];
                        $icons = ['Printing'=>'🖨️','Tech Repair'=>'💻','Delivery'=>'🚚','Food'=>'🍔','Tutoring'=>'📚','Creative'=>'🎨'];
                    @endphp
                    <button type="button"
                        @click="selectCategory({{ $category->id }}, {{ $category->services->toJson() }})"
                        :class="selectedCategory === {{ $category->id }} ? 'scale-[0.97] opacity-90' : 'hover:-translate-y-0.5'"
                        class="flex flex-col items-center p-4 border-2 rounded-2xl transition-all cursor-pointer font-medium"
                        style="background:{{ $cbg }};border-color:{{ $cborder }};color:{{ $ctext }};box-shadow:3px 3px 0 {{ $cborder }}80">
                        <span class="text-2xl mb-1">{{ $icons[$category->name] ?? '📦' }}</span>
                        <span class="text-sm font-bold">{{ $category->name }}</span>
                        <span class="text-xs mt-0.5 opacity-75">{{ $category->services->count() }} services</span>
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Service picker --}}
            <div x-show="services.length > 0" x-transition>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Choose a Service</label>
                <select name="service_id" required class="campus-input">
                    <option value="">Select service…</option>
                    <template x-for="svc in services" :key="svc.id">
                        <option :value="svc.id" x-text="svc.name + (svc.base_price ? ' — from ₵' + parseFloat(svc.base_price).toFixed(2) : '')"></option>
                    </template>
                </select>
                @error('service_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Title --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Request Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    placeholder="e.g. Print 20 pages double-sided"
                    class="campus-input @error('title') border-red-400 @enderror">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea name="description" rows="4" required
                    placeholder="Provide as much detail as possible — paper size, quantity, deadline, delivery location, etc."
                    class="campus-input resize-none @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Urgency --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">Urgency Level</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach([
                        ['low',    '#f3f4f6','#d1d5db','#6b7280', '🟢', 'Low',    'No rush'],
                        ['medium', '#b2e1eb','#8acfd1','#0e7490', '🟡', 'Medium', 'Within a day'],
                        ['high',   '#fdedc9','#f5c96b','#b8860b', '🟠', 'High',   'Today if possible'],
                        ['urgent', '#fee2e2','#fca5a5','#991b1b', '🔴', 'Urgent', 'ASAP!'],
                    ] as [$val, $bg, $border, $text, $icon, $label, $hint])
                    <label class="cursor-pointer">
                        <input type="radio" name="urgency" value="{{ $val }}" class="sr-only peer"
                            {{ old('urgency', 'medium') === $val ? 'checked' : '' }}>
                        <div class="border-2 rounded-2xl p-3 text-center transition-all hover:-translate-y-0.5 peer-checked:scale-[0.97] peer-checked:opacity-90"
                             style="background:{{ $bg }};border-color:{{ $border }};color:{{ $text }};box-shadow:3px 3px 0 {{ $border }}80">
                            <span class="text-xl block mb-1">{{ $icon }}</span>
                            <p class="text-xs font-black">{{ $label }}</p>
                            <p class="text-xs mt-0.5 opacity-75 leading-tight">{{ $hint }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('urgency') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- File attachments --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                    Attachments <span class="text-gray-400 font-normal">(optional, max 10 MB each)</span>
                </label>
                <div class="border-2 border-dashed rounded-2xl p-6 text-center transition"
                     style="border-color:#d04f99;background:rgba(208,79,153,0.03)"
                     @dragover.prevent @drop.prevent="handleDrop($event)">
                    <input type="file" name="files[]" multiple id="file-upload" class="sr-only"
                           @change="handleFiles($event)">
                    <label for="file-upload" class="cursor-pointer">
                        <p class="text-3xl mb-2">📎</p>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            <span class="text-campus-pink dark:text-campus-yellow font-bold">Click to upload</span> or drag &amp; drop
                        </p>
                        <p class="text-xs text-gray-400 mt-1">PDF, DOCX, PNG, JPG up to 10 MB</p>
                    </label>
                    <div x-show="fileNames.length > 0" class="mt-3 space-y-1">
                        <template x-for="name in fileNames" :key="name">
                            <p class="text-xs text-gray-600 dark:text-gray-400 rounded-lg px-3 py-1 inline-block"
                               style="background:#fdedc9;border:1px solid #f5c96b" x-text="'📎 ' + name"></p>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="campus-btn flex-1 justify-center py-3">
                    Submit Request →
                </button>
                <a href="{{ route('student.requests.index') }}" class="campus-btn-outline px-6 py-3">
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
