@extends('front.layouts.auth-layout')

@section('title', 'Sign In - Project City Build')
@section('meta_title', 'Sign In - Project City Build')

@section('heading', 'Sign In')

@section('content')
    <form
        method="post" action="{{ route('front.login.submit') }}"
        class="flex flex-col"
    >
        @csrf

        <h1 class="text-5xl font-bold text-gray-900 mt-2 mb-6">Sign In</h1>

        @error('error')
            <x-shared::validation-error class="mt-6">{!! $message !!}</x-shared::validation-error>
        @enderror

        @if(session()->has('success'))
            <x-shared::success-alert>{{ session()->get('success') }}</x-shared::success-alert>
        @endif

        <label for="email" class="text-md font-bold mt-6">
            Email
        </label>
        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                @error('email') border-red-500 @enderror
            "
            id="email"
            name="email"
            type="email"
            placeholder="Enter your email address"
            value="{{ old('email') }}"
        />
        @error('email')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <label for="password" class="text-md font-bold mt-6">Password</label>
        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                 @error('password') border-red-500 @enderror
            "
            id="password"
            name="password"
            type="password"
            placeholder="Enter your password"
        />
        @error('password')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <div class="flex justify-between mt-6">
            <div class="flex items-center gap-2">
                <input
                    type="checkbox"
                    name="remember_me"
                    id="remember_me"
                    class="
                        rounded-sm
                        bg-gray-300 border-0
                        checked:bg-gray-900
                    "
                    checked
                > Stay logged in
            </div>

            <a href="{{ route('front.password-reset.create') }}" class="text-sm text-blue-600">Forgot your password?</a>
        </div>

        <x-front::button type="submit" variant="filled" class="mt-12">Sign In</x-shared::button>

        <div class="mt-12 m-auto">
            <span class="text-gray-500">
                Don't have an account? <a href="{{ route('front.register') }}" class="text-blue-600 font-bold">Register for free</a>
            </span>
        </div>
    </form>
@endsection
