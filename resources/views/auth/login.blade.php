@extends('layouts.app')
@section('title', 'Sign In')

@section('content')
<div class="max-w-md mx-auto">
    {{-- Decorative header --}}
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-campus-pink flex items-center justify-center mx-auto mb-4 shadow-offset">
            <span class="text-white font-black text-2xl">C</span>
        </div>
        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Welcome back! 🎉</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Sign in to your CampusHub account</p>
    </div>

    <div class="campus-card p-8">
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="campus-input @error('email') border-red-400 @enderror"
                    placeholder="you@example.com">
                @error('email') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                <input type="password" name="password" required
                    class="campus-input"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer">
                    <input type="checkbox" name="remember"
                        class="w-4 h-4 rounded border-2 border-campus-pink accent-campus-pink">
                    Remember me
                </label>
            </div>

            <button type="submit" class="campus-btn w-full justify-center text-sm py-3">
                Sign In →
            </button>
        </form>

        <div class="mt-6 pt-5 border-t-2 border-campus-pink/10 dark:border-campus-dark-m text-center text-sm text-gray-500 dark:text-gray-400 space-y-2">
            <p>Don't have an account?
                <a href="{{ route('register') }}" class="text-campus-pink dark:text-campus-yellow font-bold hover:underline">Sign up free</a>
            </p>
        </div>
    </div>
</div>
@endsection
