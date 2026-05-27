@extends('layouts.app')
@section('title', 'Join as Provider')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-offset" style="background:#fbe2a7;border:2px solid #f5c96b">
            <span class="text-2xl">💼</span>
        </div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Become a Provider</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Offer your services to campus students</p>
    </div>

    <div class="campus-card p-8">
        <form method="POST" action="{{ route('register.provider') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="campus-input @error('name') border-red-400 @enderror"
                        placeholder="Your name">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Phone Number</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                        class="campus-input" placeholder="+233...">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="campus-input @error('email') border-red-400 @enderror"
                    placeholder="you@example.com">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Business / Service Name</label>
                <input type="text" name="business_name" value="{{ old('business_name') }}" required
                    class="campus-input"
                    placeholder="e.g. John's Printing & Tech Services">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">What services do you offer?</label>
                <textarea name="bio" rows="3"
                    class="campus-input"
                    placeholder="Briefly describe your skills and services...">{{ old('bio') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Location on Campus</label>
                    <input type="text" name="location" value="{{ old('location') }}"
                        class="campus-input" placeholder="e.g. Block C, Room 12">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">WhatsApp <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="tel" name="whatsapp" value="{{ old('whatsapp') }}"
                        class="campus-input" placeholder="+233...">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                    <input type="password" name="password" required minlength="8"
                        class="campus-input @error('password') border-red-400 @enderror"
                        placeholder="Min. 8 characters">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="campus-input" placeholder="Repeat password">
                </div>
            </div>

            <div class="rounded-xl p-4 border-2 font-medium text-sm" style="background:#fdedc9;border-color:#f5c96b;color:#b8860b">
                ℹ️ Your application will be reviewed by an admin before you can accept jobs.
            </div>

            <button type="submit" class="campus-btn w-full justify-center py-3">
                Submit Application →
            </button>
        </form>

        <div class="mt-6 pt-5 border-t-2 border-campus-pink/10 dark:border-campus-dark-m text-center text-sm text-gray-500 dark:text-gray-400">
            <p>Already have an account? <a href="{{ route('login') }}" class="text-campus-pink dark:text-campus-yellow font-bold hover:underline">Sign in</a></p>
        </div>
    </div>
</div>
@endsection
