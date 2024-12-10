@extends('front.layouts.auth-layout')

@section('title', 'Create an Account')
@section('description', 'Create a PCB account to create forum posts, access personal player statistics and more.')

@section('content')
    <form
        method="post"
        action="{{ route('front.register.submit') }}"
        id="form"
        class="flex flex-col"
    >
        @csrf

        <h1 class="text-4xl font-bold text-gray-900 mt-2 mb-6">Create an Account</h1>

        @error('error')
            <x-validation-error class="mt-6">{{ $message }}</x-validation-error>
        @enderror

        <label for="email" class="text-md font-bold mt-6">Email</label>
        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                @error('email') border-red-500 @enderror
            "
            id="email"
            name="email"
            type="email"
            placeholder="Enter your email address"
            value="{{ old('email') }}"
        />
        @error('email')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <label for="usernname" class="text-md font-bold mt-6">Username</label>
        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                @error('username') border-red-500 @enderror
            "
            id="username"
            name="username"
            type="text"
            placeholder="Pick a unique username"
            value="{{ old('username') }}"
        />
        @error('username')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <label for="password" class="text-md font-bold mt-6">Password</label>
        <input
            class="
                rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                @error('password') border-red-500 @enderror
            "
            id="password"
            name="password"
            type="password"
            placeholder="Enter a password"
            value="{{ old('password') }}"
        />
        @error('password')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <div class="
            flex items-center gap-2 mt-8 rounded-md border-2 border-gray-100 px-6 py-4
            @error('terms') border-red-500 @enderror
        ">
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
        @error('terms')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <x-captcha class="mt-6"></x-captcha>
        @error('captcha-response')
            <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
        @enderror

        <x-front::button
            type="submit"
            variant="filled"
            class="g-recaptcha mt-6"
        >
            Register
        </x-button>

        <div class="mt-12 m-auto">
            <span class="text-gray-500">
                Already have an account? <a href="{{ route('front.login') }}" class="text-blue-600 font-bold">Sign in</a>
            </span>
        </div>
    </form>
@endsection
