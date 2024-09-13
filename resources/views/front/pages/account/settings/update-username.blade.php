@extends('front.pages.account.account-layout')

@section('title', 'Account Settings')
@section('description', '')

@section('content')
    <h1 class="text-2xl font-bold">Username</h1>
    <div class="text-sm text-gray-500 mt-1">The display name for your account</div>

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
                    rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200
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

        <x-button type="submit" class="mt-6">Update</x-button>
    </form>
@endsection
