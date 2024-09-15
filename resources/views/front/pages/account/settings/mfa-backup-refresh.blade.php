@extends('front.layouts.account-settings-layout')

@section('title', 'Refresh Backup Code')
@section('description', '')

@section('content')
    <h1 class="text-2xl font-bold">Refresh Backup Code</h1>

    <hr class="my-6" />

    <p class="font-bold">Are you sure you wish to proceed?</p>
    <p class="mt-3">The old backup code will stop working and you will need to safely store the new one.</p>

    <form action="{{ route('front.account.settings.mfa.reset-backup.confirm') }}" method="post" class="flex flex-row gap-2 mt-12">
        @csrf

        <x-front::button variant="outlined" href="{{ route('front.account.settings.mfa') }}">
            Cancel
        </x-button>

        <x-front::button type="submit">
            Refresh Backup Code
        </x-button>
    </form>
@endsection
