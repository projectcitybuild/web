@extends('front.layouts.account-settings-layout')

@section('title', 'Account Settings')
@section('meta_description', 'Update the username for your Project City Build account.')

@section('content')
    <h1 class="text-2xl font-bold">Username</h1>
    <div class="text-sm text-gray-400 mt-1">The display name for your account</div>

    <hr class="my-6" />

    @error('error')
        <x-validation-error class="mt-6">{{ $message }}</x-validation-error>
    @enderror

    @if(session()->has('success'))
        <x-success-alert class="mt-6">{{ session()->get('success') }}</x-success-alert>
    @endif

    <form
        method="post"
        action="{{ route('front.account.settings.username') }}"
        class="flex flex-col items-start"
    >
        @csrf

        <input
            class="
                    rounded-md border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-gray-100 placeholder-gray-400
                    @error('username') border-red-500 @enderror
                "
            id="username"
            name="username"
            type="text"
            placeholder="Enter your email address"
            value="{{ old('username', Auth::user()->username) }}"
        />
        @error('username')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <x-front::button type="submit" class="mt-6">Update</x-button>
    </form>
@endsection
