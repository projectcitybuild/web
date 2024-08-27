@extends('front.pages.auth.auth-layout')

@section('title', 'Sign In - Project City Build')
@section('meta_title', 'Sign In - Project City Build')

@section('heading', 'Sign In')

@section('content')
    <form
        method="post" action="{{ route('front.login.submit') }}"
        class="flex flex-col"
    >
        @csrf

        <h1 class="text-5xl font-bold text-gray-900 mt-2 mb-6">Sign In</h1>

        @if ($errors->any())
            <x-validation-error message="{{ $errors }}" class="mt-6">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-validation-error>
        @endif

        @if(Session::get('mfa_removed', false))
            <div class="alert alert--success">
                <h2><i class="fas fa-check"></i> 2FA Reset</h2>
                2FA has been removed from your account, please sign in again.
            </div>
        @endif
        @if(session()->has('success'))
            <div class="alert alert--success">
                <h2><i class="fas fa-check"></i> Success</h2>
                {{ session()->get('success') }}
            </div>
        @endif

        <label for="email" class="text-md font-bold mt-6">
            Email
        </label>
        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
            "
            id="email"
            name="email"
            type="email"
            placeholder="Enter your email address"
            value="{{ old('email') }}"
        />

        <label for="password" class="text-md font-bold mt-6">Password</label>
        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
            "
            id="password"
            name="password"
            type="password"
            placeholder="Enter your password"
        />

        <div class="flex justify-between mt-6">
            <div class="flex items-center gap-2">
                <input
                    type="checkbox"
                    name="remember_me"
                    id="remember_me"
                    class="
                        rounded-sm
                        bg-gray-300 border-0
                        checked:bg-gray-900
                    "
                    checked
                > Stay logged in
            </div>

            <a href="{{ route('front.password-reset.create') }}" class="text-sm text-blue-600">Forgot your password?</a>
        </div>

        <x-filled-button type="submit" class="mt-12">Sign In</x-filled-button>

        <div class="mt-12 m-auto">
            <span class="text-gray-500">
                Don't have an account? <a href="{{ route('front.register') }}" class="text-blue-600 font-bold">Register for free</a>
            </span>
        </div>
    </form>
@endsection
