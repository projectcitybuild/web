@extends('front.layouts.auth-layout')

@section('title', 'Confirm Your Identity')
@section('description', '')

@section('content')
    <form method="post" action="{{ route('front.oidc.reauth.submit') }}" class="flex flex-col">
        @csrf
        <input type="hidden" name="transaction" value="{{ request('transaction') }}">
        <input type="hidden" name="challenge" value="{{ request('challenge') }}">

        <h1 class="text-4xl font-bold text-gray-900 mt-2 mb-6">Confirm Your Identity</h1>

        @if ($mfaPending)
            <p class="text-gray-500 mt-4">Enter the 6-digit code from your authenticator app.</p>
            <label for="code" class="text-md font-bold mt-6">Authentication code</label>
            <input
                id="code"
                name="code"
                type="text"
                inputmode="numeric"
                autocomplete="one-time-code"
                maxlength="6"
                class="rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2"
                autofocus
            >
            @error('code')
                <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
            @enderror
        @else
            <label for="email" class="text-md font-bold mt-4">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                autocomplete="username"
                value="{{ old('email', $email) }}"
                class="rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2"
                required
            >
            @error('email')
                <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
            @enderror

            <label for="password" class="text-md font-bold mt-6">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                class="rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2"
                required
            >
            @error('password')
                <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
            @enderror
        @endif

        <x-front::button type="submit" variant="filled" class="mt-8">Continue</x-button>
    </form>
@endsection