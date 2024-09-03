@extends('front.pages.auth.auth-layout')

@section('title', 'Registration Verification')
@section('description', "")

@section('content')
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
    </svg>

    <h1 class="text-4xl font-bold text-gray-900 mt-6">Verify Your Email</h1>

    @if(session()->has('success'))
        <x-success-alert class="mt-6">{{ session()->get('success') }}</x-success-alert>
    @endif

    <div class="text-gray-500 mt-6">
        An email has been sent to <strong>{{ $email }}</strong> with a link to verify your account. If you have not received the email
        after a few minutes, please check your spam folder.
    </div>

    <form method="post" action="{{ route('front.activate.resend', ['email' => $email]) }}" class="flex flex-row gap-2 mt-12">
        @csrf
        <x-button type="submit" variant="filled">Resend email</x-button>

        <x-button variant="outlined" href="{{ route('front.logout') }}">Back to login</x-button>
    </form>
@endsection
