@extends('front.layouts.account-settings-layout')

@section('title', 'Account Settings')
@section('description', '')

@section('content')
    <h1 class="text-2xl font-bold">Email Address</h1>
    <div class="text-sm text-gray-500 mt-1">The email address associated with your account</div>

    <hr class="my-6" />

    @error('error')
        <x-shared::validation-error class="mt-6">{{ $message }}</x-shared::validation-error>
    @enderror

    @if(session()->has('success'))
        <x-shared::success-alert class="mt-6">{{ session()->get('success') }}</x-shared::success-alert>
    @endif

    <p class="text-gray-500 mt-2">An email will be sent to your new email address with a link to complete the change.</p>

    <form
        method="post"
        action="{{ route('front.account.settings.email') }}"
        class="flex flex-col items-start"
    >
        @csrf

        <input
            class="
                    rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-6
                    @error('email') border-red-500 @enderror
                "
            id="email"
            name="email"
            type="email"
            placeholder="Enter your email address"
            value="{{ old('email', Auth::user()->email) }}"
        />
        @error('email')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <x-front::button type="submit" class="mt-6">Update</x-shared::button>
    </form>
@endsection
