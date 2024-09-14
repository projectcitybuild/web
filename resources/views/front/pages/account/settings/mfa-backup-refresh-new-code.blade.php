@extends('front.layouts.account-settings-layout')

@section('title', 'Refresh 2FA Backup')
@section('description', '')

@section('content')
    <h1 class="text-2xl font-bold">Refresh Backup Code</h1>

    <hr class="my-6" />

    <div>
        <label>Your new backup code:</label>
        <div class="rounded-md border-2 border-dashed text-center text-2xl p-6 font-bold mt-2 overflow-x-auto">{{ $backupCode }}</div>
    </div>

    <div class="text-red-500 mt-6">
        You <strong>must</strong> store this code. It is the only way to regain access to your account if you lose your 2FA device.
    </div>

    <x-front::button href="{{ route('front.account.settings.mfa') }}" class="mt-6">
        Finish
    </x-shared::button>
@endsection

