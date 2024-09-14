@extends('front.layouts.account-settings-layout')

@section('title', 'Account Settings')
@section('description', '')

@section('content')
    <h1 class="text-2xl font-bold">Password</h1>
    <div class="text-sm text-gray-500 mt-1">The password used to sign-in to your account</div>

    <hr class="my-6" />

    @error('error')
        <x-shared::validation-error class="mt-6">{{ $message }}</x-shared::validation-error>
    @enderror

    @if(session()->has('success'))
        <x-shared::success-alert class="mt-6">{{ session()->get('success') }}</x-shared::success-alert>
    @endif

    <form
        method="post"
        action="{{ route('front.account.settings.password.store') }}"
        class="flex flex-col items-start"
    >
        @csrf

        <label for="old_password" class="text-md font-bold">Current Password</label>
        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200
                @error('old_password') border-red-500 @enderror
            "
            id="old_password"
            name="old_password"
            type="password"
            placeholder="Password"
            value="{{ old('old_password') }}"
        />
        @error('old_password')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <label for="new_password" class="text-md font-bold mt-6">New Password</label>
        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200
                @error('new_password') border-red-500 @enderror
            "
            id="new_password"
            name="new_password"
            type="password"
            placeholder="Password"
            value="{{ old('new_password') }}"
        />
        @error('new_password')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <label for="new_password_confirm" class="text-md font-bold mt-6">Confirm New Password</label>
        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200
                @error('new_password_confirm') border-red-500 @enderror
            "
            id="new_password_confirm"
            name="new_password_confirm"
            type="password"
            placeholder="Password"
            value="{{ old('new_password_confirm') }}"
        />
        @error('new_password_confirm')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <x-front::button type="submit" class="mt-6">Update</x-shared::button>
    </form>
@endsection
