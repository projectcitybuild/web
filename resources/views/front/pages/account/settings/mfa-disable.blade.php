@extends('front.pages.account.account-layout')

@section('title', 'Disable 2FA')
@section('description', '')

@section('content')
    <h1 class="text-2xl font-bold">Disable 2FA</h1>

    <hr class="my-6" />

    <p class="font-bold">Are you sure you wish to proceed?</p>
    <p class="mt-3">
        You will no longer need the registered device to authenticate. You can re-enable 2FA at any time, but
        you will be given a different code and backup code.
    </p>

    <form action="{{ route('front.account.settings.mfa.disable.confirm') }}" method="post" class="flex flex-row gap-2 mt-12">
        @csrf
        @method('DELETE')

        <x-button variant="outlined" href="{{ route('front.account.settings.mfa') }}">
            Cancel
        </x-button>

        <x-button type="submit">
            Disable 2FA
        </x-button>
    </form>
@endsection
