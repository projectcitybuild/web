@extends('front.layouts.root-layout')

@section('title', 'Appeal Ban')
@section('heading', 'Appeal Ban')
@section('description', 'Use the below form to submit a ban appeal')

@section('body')
    <x-front::navbar />

    <main class="bg-white py-20">
        <header class="max-w-screen-2xl mx-auto px-6">
            @if($errors->any())
                <div class="alert alert--error">
                    <h2><i class="fas fa-exclamation-circle"></i> Error</h2>
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="-mx-4 flex flex-wrap">
                <div class="w-full px-4">
                    <div class="mx-auto mb-[60px] max-w-[650px] text-center">
                        <h2 class="font-bold text-5xl">
                            Select the Ban
                        </h2>
                        <p class="text-base text-gray-500 mt-4 leading-relaxed">
                            Please search for the ban you wish to appeal.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <section class="max-w-screen-lg mx-auto px-6">
            <h2 class="text-lg font-bold text-center">Your Active Bans <span class="font-normal text-gray-500 text-sm">(1 record)</span></h2>

{{--            <div class="text-center text-gray-500 text-sm mt-3">--}}
{{--                None of the Minecraft accounts linked to your account have an active ban.<br />Please search the below list or <a href="{{ route('front.appeal.form') }}" class="text-blue-500">fill out the form</a> manually--}}
{{--            </div>--}}

            <div class="rounded-lg border border-gray-200 mt-3 bg-gray-50">
                <div class="flex flex-row justify-between items-center border-b bg-gray-100 py-3 px-4">
                    <div class="font-semibold">
                        21st of January, 2024 <span class="text-gray-500">- 19:38 UTC</span>
                    </div>
                </div>

                <div class="py-3 px-4">
                    <div class="mt-1 flex flex-col gap-1">
                        <img src="https://minotar.net/avatar/Notch/32" class="rounded-md w-12">
                        <strong class="text-3xl">Notch</strong>
                        <span class="text-gray-500 text-xs">(UUID: <strong>069a79f4-44e9-4726-a5be-fca90e38aaf5</strong>)</span>
                    </div>

                    <hr class="my-6" />

                    <div class="flex flex-row flex-wrap gap-12">
                        <div>
                            <div class="text-gray-500 text-sm">Expires</div>
                            <div class="mt-1">Never</div>
                        </div>
                        <div>
                            <div class="text-gray-500 text-sm">Banned by</div>
                            <div class="mt-1">Console</div>
                        </div>
                        <div>
                            <div class="text-gray-500 text-sm">Reason for Ban</div>
                            <div class="mt-1">Some ban reason goes here eventually</div>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-3">
                    <x-button size="sm">
                        Select
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </x-button>
                </div>
            </div>
        </section>

        <section class="max-w-screen-lg mx-auto px-6 mt-12">
            <table class="w-full overflow-x-auto">
                <thead>
                <tr class="text-left text-sm text-gray-500 bg-gray-100">
                    <th class="py-4 px-2">Player Name</th>
                    <th class="py-4 px-2">Reason</th>
                    <th class="py-4 px-2">Ban Date</th>
                    <th class="py-4 px-2">Action</th>
                </tr>
                </thead>
                <tbody>
{{--                    @foreach($bans as $ban)--}}
{{--                        <tr>--}}
{{--                            <td class="py-4 px-2 flex gap-2">--}}
{{--                                <img src="https://minotar.net/avatar//{{ $ban->bannedPlayer->uuid }}/16" class="rounded-md h-6">--}}
{{--                                <span class="font-bold">{{ $ban->banned_alias_at_time }}</span>--}}
{{--                            </td>--}}
{{--                            <td class="py-4 px-2">--}}
{{--                                {{ $ban->reason ?? "-" }}--}}
{{--                            </td>--}}
{{--                            <td class="py-4 px-2">--}}
{{--                                {{ $ban->created_at->format('j M Y H:i') }}--}}
{{--                            </td>--}}
{{--                            <td class="py-4 px-2">--}}
{{--                                <x-button href="{{ route('front.bans.details', $ban) }}" size="sm">--}}
{{--                                    View Details--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">--}}
{{--                                        <path fill-rule="evenodd" d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />--}}
{{--                                    </svg>--}}
{{--                                </x-button>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
                </tbody>
            </table>
        </section>
    </main>
@endsection
