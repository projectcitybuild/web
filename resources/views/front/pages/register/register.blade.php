@extends('front.pages.auth.auth-layout')

@section('title', 'Create an Account')
@section('description', 'Create a PCB account to create forum posts, access personal player statistics and more.')

@push('head')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function submitForm() {
            document.getElementById('form').submit();
        }
    </script>
@endpush

@section('content')
    <form
        method="post"
        action="{{ route('front.register.submit') }}"
        id="form"
        class="flex flex-col"
    >
        @csrf

        <h1 class="text-4xl font-bold text-gray-900 mt-2 mb-6">Create an Account</h1>

        @include('front.components.form-error')

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

        <label for="email" class="text-md font-bold mt-6">
            Username
        </label>
        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
            "
            id="username"
            name="username"
            type="text"
            placeholder="Pick a unique username"
            value="{{ old('username') }}"
        />

        <label for="password" class="text-md font-bold mt-6">Password</label>
        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
            "
            id="password"
            name="password"
            type="password"
            placeholder="Enter a password"
            value="{{ old('password') }}"
        />

        <div class="flex items-center gap-2 mt-8 rounded-md border-2 border-gray-100 px-6 py-4">
            <input
                type="checkbox"
                name="terms"
                {{ old('terms') ? 'checked' : '' }}
                class="
                        rounded-sm
                        bg-gray-300 border-0
                        checked:bg-gray-900
                    "
                value="1"
            >
            <span class="text-xs">
                I agree to the
                <a href="{{ route('terms') }}" class="text-blue-600" target="_blank">terms and conditions</a> and
                <a href="{{ route('privacy') }}" class="text-blue-600" target="_blank">privacy policy</a>
            </span>
        </div>

        <x-filled-button
            type="submit"
            class="g-recaptcha mt-6"
            data-sitekey="@recaptcha_key"
            data-callback="submitForm"
        >
            Register
        </x-filled-button>

        <div class="mt-12 m-auto">
            <span class="text-gray-500">
                Already have an account? <a href="{{ route('front.login') }}" class="text-blue-600 font-bold">Sign in</a>
            </span>
        </div>
    </form>
@endsection
