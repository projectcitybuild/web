@extends('front.templates.master')

@section('title', 'Account Settings - Your Account - Project City Build')
@section('description', '')

@section('body')
    <header class="image-header">
        <div class="container">
            <h1>Your Account</h1>
        </div>
    </header>

    <main class="page settings">
        @include('front.pages.account.components.account-sidebar')

        <div class="settings__content">

            <div class="settings__section" id="change-email">
                @error('error')
                    <x-validation-error class="mt-6">{{ $message }}</x-validation-error>
                @enderror

                @if(session()->has('success'))
                    <x-success-alert class="mt-6">{{ session()->get('success') }}</x-success-alert>
                @endif

                <h1 class="text-2xl font-bold text-gray-900">Change Email Address</h1>

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

                    <x-button type="submit" class="mt-6">Change</x-button>
                </form>
            </div>

            <div class="settings__section" id="change-username">
                <h1 class="text-2xl font-bold text-gray-900">Change Username</h1>

                <form
                    method="post"
                    action="{{ route('front.account.settings.username') }}"
                    class="flex flex-col items-start"
                >
                    @csrf

                    <input
                        class="
                            rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-6
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

                    <x-button type="submit" class="mt-6">Change</x-button>
                </form>
            </div>
        </div>
    </main>

    @include('front.components.footer')
@endsection
