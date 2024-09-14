@extends('front.layouts.auth-layout')

@section('title', 'Re-enter Password')
@section('description', '')

@section('content')
    <form action="{{ route('front.password.confirm.submit') }}" method="post" class="flex flex-col">
        @csrf

        <h1 class="text-4xl font-bold text-gray-900 mt-2">Reauthenticate</h1>

        @error('error')
        <x-shared::validation-error class="mt-6">{{ $message }}</x-shared::validation-error>
        @enderror


        <div class="text-gray-500 mt-6">Please re-enter your password to continue</div>

        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                @error('password') border-red-500 @enderror
            "
            id="password"
            name="password"
            type="password"
            placeholder="Password"
        />
        @error('password')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <x-front::button type="submit" variant="filled" class="mt-6">Confirm</x-shared::button>
    </form>
@endsection
