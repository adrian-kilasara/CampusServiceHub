@extends('layouts.app')
@section('title', 'Create Student Account')

@section('content')
<div class="max-w-md mx-auto">
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-campus-teal flex items-center justify-center mx-auto mb-4 shadow-offset-tl">
            <span class="text-white font-black text-2xl">🎓</span>
        </div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Create Account</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Join CampusHub as a student</p>
    </div>

    <div class="campus-card p-8">
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="campus-input @error('name') border-red-400 @enderror"
                    placeholder="Your full name">
                @error('name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="campus-input @error('email') border-red-400 @enderror"
                    placeholder="you@university.edu">
                @error('email') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Phone <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                        class="campus-input" placeholder="+233...">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Student ID <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="text" name="student_id" value="{{ old('student_id') }}"
                        class="campus-input" placeholder="e.g. UG0012345">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                <input type="password" name="password" required minlength="8"
                    class="campus-input @error('password') border-red-400 @enderror"
                    placeholder="Min. 8 characters">
                @error('password') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="campus-input" placeholder="Repeat password">
            </div>

            <button type="submit" class="campus-btn w-full justify-center py-3">
                Create Account →
            </button>
        </form>

        <div class="mt-6 pt-5 border-t-2 border-campus-pink/10 dark:border-campus-dark-m text-center text-sm text-gray-500 dark:text-gray-400 space-y-2">
            <p>Already have an account?
                <a href="{{ route('login') }}" class="text-campus-pink dark:text-campus-yellow font-bold hover:underline">Sign in</a>
            </p>
            <p>Want to offer services?
                <a href="{{ route('register.provider') }}" class="text-campus-teal-h dark:text-campus-teal font-bold hover:underline">Register as Provider</a>
            </p>
        </div>
    </div>
</div>
@endsection
