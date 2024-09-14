@extends('front.layouts.account-settings-layout')

@section('title', 'Account Settings')
@section('description', '')

@section('content')
    <h1 class="text-2xl font-bold">2-Factor Authentication</h1>
    <div class="text-sm text-gray-500 mt-1">Protect your account by requiring a one-time code generated by your phone to sign in.</div>

    <hr class="my-6" />

    @error('error')
        <x-validation-error class="my-6">{{ $message }}</x-validation-error>
    @enderror

    @if(session()->has('success'))
        <x-success-alert class="my-6">{{ session()->get('success') }}</x-success-alert>
    @endif

    @if(Session::get('mfa_setup_required', false))
        <div class="alert alert--error">
            <h2><i class="fas fa-exclamation-circle"></i> 2FA is disabled</h2>
            You need to set up 2FA to use {{ Session::get('mfa_setup_required_feature', 'this feature') }}.
        </div>
    @endif

    @if(Auth::user()->is_totp_enabled)
        <div class="rounded-md border-2 border-green-300 p-6">
            <p class="flex flex-row gap-1 text-xl font-bold text-green-500 mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 0 0-1.032 0 11.209 11.209 0 0 1-7.877 3.08.75.75 0 0 0-.722.515A12.74 12.74 0 0 0 2.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 0 0 .374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 0 0-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08Zm3.094 8.016a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                </svg>
                Enabled
            </p>
            <p class="text-gray-500">You will need to use your device or your backup code to sign in.</p>
        </div>

        <div class="flex flex-row gap-2 mt-6">
            <x-front::button href="{{ route('front.account.settings.mfa.reset-backup') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z" clip-rule="evenodd" />
                </svg>
                Regenerate Backup Code
            </x-button>

            <x-front::button href="{{ route('front.account.settings.mfa.disable') }}" variant="outlined">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M14.5 1A4.5 4.5 0 0 0 10 5.5V9H3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-1.5V5.5a3 3 0 1 1 6 0v2.75a.75.75 0 0 0 1.5 0V5.5A4.5 4.5 0 0 0 14.5 1Z" clip-rule="evenodd" />
                </svg>
                Disable 2FA
            </x-button>
        </div>
    @else
        <div class="rounded-md bg-gray-100 p-6">
            <p class="flex flex-row gap-1 text-xl font-bold text-gray-900 mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                </svg>
                Not Enabled
            </p>
            <p class="text-gray-500">You don't have 2FA enabled yet. It's optional, but it will help protect your account.</p>
        </div>

        <form action="{{ route('front.account.settings.mfa.start') }}" method="post" class="mt-6">
            @csrf
            <x-front::button type="submit">Set Up</x-button>
        </form>
    @endif
@endsection
