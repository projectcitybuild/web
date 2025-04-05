@extends('front.layouts.root-layout')

@section('title', 'Player')
@section('description', '')

@section('body')
    <x-account-navbar />

    <main
        class="
            flex flex-col max-w-screen-xl
            md:flex-row md:mx-auto md:mt-8
        "
    >
        <div class="rounded-lg bg-white flex-grow p-6 m-2">
            <section class="mb-6">
                <h1 class="text-2xl font-bold mb-3">Minecraft Account</h1>
                <p>The Minecraft account linked to this account. By linking, you will automatically receive your rank in-game.</p>
            </section>

            @if($mcAccounts->count() == 0)
                <div class="rounded-md border-2 border-gray-100 my-6 p-6 flex flex-row items-center gap-2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.181 8.68a4.503 4.503 0 0 1 1.903 6.405m-9.768-2.782L3.56 14.06a4.5 4.5 0 0 0 6.364 6.365l3.129-3.129m5.614-5.615 1.757-1.757a4.5 4.5 0 0 0-6.364-6.365l-4.5 4.5c-.258.26-.479.541-.661.84m1.903 6.405a4.495 4.495 0 0 1-1.242-.88 4.483 4.483 0 0 1-1.062-1.683m6.587 2.345 5.907 5.907m-5.907-5.907L8.898 8.898M2.991 2.99 8.898 8.9" />
                    </svg>
                    <p>You have not linked a Minecraft account</p>
                </div>
            @else
                @foreach($mcAccounts as $mcAccount)
                    <div class="rounded-md bg-gray-100 p-6 flex flex-row gap-4 content-between items-center flex-wrap">
                        <img src="https://minotar.net/avatar/{{ $mcAccount->uuid }}/64" class="rounded-md" alt="">

                        <div class="flex-grow">
                            <div class="text-xl font-bold">
                                @if($mcAccount->alias === null)
                                    <em>No known name</em>
                                @else
                                    {{ $mcAccount->alias }}
                                @endempty
                            </div>
                            <div class="text-sm">UUID: {{ $mcAccount->uuid }}</div>
                            <div class="text-sm text-gray-500">
                                @isset($mcAccount->last_seen_at)
                                    <span>Last seen {{ $mcAccount->last_seen_at->diffForHumans() }}</span>
                                @else
                                    <span>Not seen online</span>
                                @endif
                            </div>
                        </div>

                        <form action="{{ route('front.account.games.delete', $mcAccount) }}" method="post">
                            @csrf
                            @method('delete')
                            <x-front::button type="submit" variant="outlined">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M2.22 2.22a.75.75 0 0 1 1.06 0l4.46 4.46c.128-.178.272-.349.432-.508l3-3a4 4 0 0 1 5.657 5.656l-1.225 1.225a.75.75 0 1 1-1.06-1.06l1.224-1.225a2.5 2.5 0 0 0-3.536-3.536l-3 3a2.504 2.504 0 0 0-.406.533l2.59 2.59a2.49 2.49 0 0 0-.79-1.254.75.75 0 1 1 .977-1.138 3.997 3.997 0 0 1 1.306 3.886l4.871 4.87a.75.75 0 1 1-1.06 1.061l-5.177-5.177-.006-.005-4.134-4.134a.65.65 0 0 1-.005-.006L2.22 3.28a.75.75 0 0 1 0-1.06Zm3.237 7.727a.75.75 0 0 1 0 1.06l-1.225 1.225a2.5 2.5 0 0 0 3.536 3.536l1.879-1.879a.75.75 0 1 1 1.06 1.06L8.83 16.83a4 4 0 0 1-5.657-5.657l1.224-1.225a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                                </svg>
                                Unlink
                            </x-button>
                        </form>
                    </div>
                @endforeach
            @endif

            <hr class="my-6" />

            <section>
                <h1 class="text-2xl font-bold mb-3">How to Link</h1>

                <p class="settings__description">To add (or switch) your Minecraft account, type <strong>/sync</strong> in-game and follow the instructions.</p>
            </section>
        </div>
    </main>
@endsection
