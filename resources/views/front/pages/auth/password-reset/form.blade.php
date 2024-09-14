@extends('front.layouts.auth-layout')

@section('title', 'Forgot Your Password?')
@section('description', "If you've forgotten your PCB password but remember your email address, use this form to reset your password.")

@section('content')
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
    </svg>

    <h1 class="text-4xl font-bold text-gray-900 mt-6">Forgot Your Password?</h1>

    <div class="text-gray-500 mt-6">
        It happens. Enter your email address and we'll send you a link to set a new password.
    </div>

    <form
        method="post"
        action="{{ route('front.password-reset.store') }}"
        id="form"
        class="flex flex-col"
    >
        @csrf

        @error('error')
            <x-shared::validation-error class="mt-6">{{ $message }}</x-shared::validation-error>
        @enderror

        @if(session()->has('success'))
            <x-shared::success-alert class="mt-6">{{ session()->get('success') }}</x-shared::success-alert>
        @endif

        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-8
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

        <x-front::button type="submit" variant="filled" class="mt-8">
            Send Reset Link
        </x-shared::button>

        <a class="flex items-center gap-2 mt-12 text-sm text-gray-500 m-auto" href="{{ route('front.login') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Sign In
        </a>
    </form>
@endsection
