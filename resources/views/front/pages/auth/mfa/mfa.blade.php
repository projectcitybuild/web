@extends('front.pages.auth.auth-layout')

@section('title', '2FA Confirmation')
@section('description', '')

@section('content')
    <form
        method="post"
        action="{{ route('front.login.mfa.submit') }}"
        id="form"
        class="flex flex-col"
    >
        @csrf

        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
        </svg>

        <h1 class="text-4xl font-bold text-gray-900 mt-6">Two Factor Authentication</h1>

        <div class="text-gray-500 mt-6">
            Enter the 6-digit code from your authenticator app to continue
        </div>

        <div class="flex justify-content-between items-center gap-2 mt-12">
            <input
                class="
                    rounded-md bg-gray-100 px-1 py-3 text-2xl border-gray-200 text-center
                    @error('code') border-red-500 @enderror
                "
                id="code"
                name="code"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                autocomplete="one-time-code"
                placeholder="000000"
                size="6"
                maxlength="6"
                autofocus
            />
        </div>
        @error('code')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <div class="mt-12">
            <x-button type="submit" variant="filled">Verify</x-button>
        </div>

        <a class="flex items-center gap-2 mt-12 text-sm text-gray-500 m-auto" href="{{ route('front.login.mfa-recover') }}">
            Unable to verify?
        </a>

        <a class="flex items-center gap-2 mt-12 text-sm text-gray-500 m-auto" href="{{ route('front.logout') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Cancel and go back
        </a>
    </form>
@endsection
