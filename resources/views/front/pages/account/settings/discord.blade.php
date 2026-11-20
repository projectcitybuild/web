@extends('front.layouts.account-settings-layout')

@section('title', 'Discord Account')
@section('description', '')

@section('content')
    <h1 class="text-2xl font-bold">Discord account</h1>

    <hr class="my-6" />

    @if(session()->has('success'))
        <x-success-alert>{{ session()->get('success') }}</x-success-alert>
    @endif

    @if(session()->has('error'))
        <p class="mb-4 text-red-700" role="alert">{{ session()->get('error') }}</p>
    @endif

    @if($externalAccount)
        <p class="mb-4">
            Linked as <span class="font-bold">{{ $externalAccount->username }}</span>.
        </p>

        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('front.account.settings.discord.sync') }}">
                @csrf
                <button class="rounded bg-blue-700 px-4 py-2 text-white" type="submit">
                    Request role sync
                </button>
            </form>

            <form method="POST" action="{{ route('front.account.settings.discord.unlink') }}">
                @csrf
                @method('DELETE')
                <button class="rounded border border-gray-400 px-4 py-2" type="submit">
                    Unlink Discord
                </button>
            </form>
        </div>
    @else
        <p class="mb-4">Link your Discord account to synchronize your website roles.</p>
        <a
            class="inline-block rounded bg-blue-700 px-4 py-2 text-white"
            href="{{ route('front.account.settings.discord.connect') }}"
        >
            Link Discord account
        </a>
    @endif
@endsection
