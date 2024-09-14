@extends('front.layouts.account-settings-layout')

@section('title', 'Setup 2FA')
@section('description', '')

@section('content')
    <h1 class="text-2xl font-bold">Enable 2FA</h1>
    <div class="text-sm text-gray-500 mt-1">Follow the steps below to enable 2-factor Authentication on your account.</div>

    <hr class="my-6" />

    <form action="{{ route('front.account.settings.mfa.finish') }}" method="post">
        @csrf

        @error('error')
            <x-validation-error class="mt-6">{{ $message }}</x-validation-error>
        @enderror

        <section>
            <h2 class="text-xl font-bold flex flex-row items-center gap-2 mb-3">
                <span class="rounded-md bg-gray-100 px-2 py-1 text-sm">Step 1</span>
                Download a 2FA app
            </h2>
            <p>
                You'll need an app on your phone to set up 2-factor authentication, like
                <a href="https://www.microsoft.com/en-us/account/authenticator" class="text-blue-500">Microsoft Authenticator</a>, or
                <a href="https://apps.apple.com/us/app/raivo-authenticator/id1459042137" class="text-blue-500">Raivo</a> (iOS).
            </p>
        </section>

        <hr class="my-6" />

        <section>
            <h2 class="text-xl font-bold flex flex-row items-center gap-2 mb-3">
                <span class="rounded-md bg-gray-100 px-2 py-1 text-sm">Step 2</span>
                Save your backup code
            </h2>

            <p>You need to save your backup code somewhere safe. If you ever lose access to your
                authentication
                app you can use this to disable 2FA on your account.</p>

            <div class="my-6">
                <label>Your backup code:</label>
                <div class="rounded-md border-2 border-dashed text-center text-2xl p-6 font-bold mt-2 overflow-x-auto">{{ $backupCode }}</div>
            </div>

            <p>Please check below to confirm you've saved this:</p>

            <div class="rounded-md bg-gray-100 p-6 @error('backup_saved') bg-red-500 @enderror">
                <label class="flex flex-row gap-4 items-center">
                    <input
                        type="checkbox"
                        name="backup_saved"
                        class="
                            rounded-sm
                            bg-gray-300 border-0
                            checked:bg-gray-900
                        "
                        value="1"
                        @if(old('backup_saved', 0) == 1) checked @endif
                    >
                    I've saved this code somewhere safe
                </label>
            </div>
            @error('backup_saved')
                <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
            @enderror

        </section>

        <hr class="my-6" />

        <div class="settings__section">
            <h2 class="text-xl font-bold flex flex-row items-center gap-2 mb-3">
                <span class="rounded-md bg-gray-100 px-2 py-1 text-sm">Step 3</span>
                Scan the QR Code
            </h2>

            <div>
                <p>Scan this QR Code using your 2FA app:</p>

                <div class="mt-6">
                    <div>{!! $qrSvg !!}</div>

                    <div class="text-gray-500 mt-6">
                        If you can't scan the code, manually enter the key
                        <div class="font-bold mt-2 text-lg text-gray-900">{{ $secretKey }}</div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-6" />

        <div class="settings__section">
            <h2 class="text-xl font-bold flex flex-row items-center gap-2 mb-3">
                <span class="rounded-md bg-gray-100 px-2 py-1 text-sm">Step 4</span>
                Enter a Code
            </h2>

            <p>Enter the current code your 2FA app generates:</p>

            @csrf

            <div class="flex flex-col items-start">
                <label for="code" class="text-md font-bold mt-6">2FA Code</label>
                <input
                    class="
                        rounded-md bg-gray-100 px-4 py-3 text-sm border-gray-200 mt-2
                         @error('code') border-red-500 @enderror
                    "
                    maxlength="6"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    autocomplete="one-time-code"
                    size="6"
                    name="code"
                    id="code"
                    type="text"
                />
                @error('code')
                    <span class="text-sm text-red-500 mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <hr class="my-6" />

        <x-front::button type="submit" class="mt-6">Enable</x-button>
    </form>
@endsection
