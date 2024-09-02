@extends('front.pages.auth.auth-layout')

@section('title', '2FA Recovery')
@section('description', '')

@section('content')
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
    </svg>

    <h1 class="text-4xl font-bold text-gray-900 mt-6">Can't verify?</h1>

    <div class="text-gray-500 mt-6">
        Enter your backup code to remove MFA on your account. If you've lost your backup code, please contact us on <a href="{{ config('discord.invite_url') }}" class="text-blue-600">Discord</a>.
    </div>

    <form
        method="post"
        action="{{ route('front.login.mfa-recover.submit') }}"
        id="form"
        class="flex flex-col"
    >
        @csrf
        @method('DELETE')

        @error('error')
            <x-validation-error class="mt-6">{{ $message }}</x-validation-error>
        @enderror

        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-8
                @error('backup_code') border-red-500 @enderror
            "
            id="backup_code"
            name="backup_code"
            type="text"
            placeholder="Enter your backup code"
            value="{{ old('backup_code') }}"
        />
        @error('backup_code')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <x-button type="submit" variant="filled" class="mt-8">
            Recover
        </x-button>

        <a class="flex items-center gap-2 mt-12 text-sm text-gray-500 m-auto" href="{{ route('front.login.mfa') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Code Verification
        </a>
    </form>
@endsection
