@extends('front.layouts.root-layout')

@section('title', 'Records')
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
            <section>
                <div class="flex flex-row gap-2 justify-between items-center mb-3">
                    <h1 class="text-2xl font-bold">Builder Rank Applications</h1>

                    <div class="flex flex-row gap-1 items-center text-blue-500">
                        <a href="{{ route('front.rank-up') }}" class="text-sm">Create Application</a>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path fill-rule="evenodd" d="M2 10a.75.75 0 0 1 .75-.75h12.59l-2.1-1.95a.75.75 0 1 1 1.02-1.1l3.5 3.25a.75.75 0 0 1 0 1.1l-3.5 3.25a.75.75 0 1 1-1.02-1.1l2.1-1.95H2.75A.75.75 0 0 1 2 10Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                @if($builderRankApplications->count() == 0)
                    <div class="rounded-md border-2 border-gray-100 my-6 p-6 flex flex-row items-center gap-2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <p>No records found</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full my-6">
                            <thead>
                            <tr class="text-left bg-gray-100">
                                <th class="p-2">Date</th>
                                <th class="p-2">Status</th>
                                <th class="p-2">Minecraft Alias</th>
                                <th class="p-2">Build Location</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($builderRankApplications as $app)
                                <tr>
                                    <td class="p-2">
                                        <a href="{{ route('front.rank-up.status', $app) }}" class="text-blue-500">
                                            {{ $app->created_at?->utc() ?? "Unknown" }}
                                        </a>
                                    </td>
                                    <td class="p-2">{{ $app->status->humanReadable() }}</td>
                                    <td class="p-2">{{ $app->minecraft_alias }}</td>
                                    <td class="p-2">{{ $app->build_location }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <hr class="my-6" />

            <section>
                <div class="flex flex-row gap-2 justify-between items-center mb-3">
                    <h1 class="text-2xl font-bold">Ban Appeals</h1>

                    <div class="flex flex-row gap-1 items-center text-blue-500">
                        <a href="{{ route('front.appeal') }}" class="text-sm">Create Appeal</a>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                            <path fill-rule="evenodd" d="M2 10a.75.75 0 0 1 .75-.75h12.59l-2.1-1.95a.75.75 0 1 1 1.02-1.1l3.5 3.25a.75.75 0 0 1 0 1.1l-3.5 3.25a.75.75 0 1 1-1.02-1.1l2.1-1.95H2.75A.75.75 0 0 1 2 10Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                @if($banAppeals->count() == 0)
                    <div class="rounded-md border-2 border-gray-100 my-6 p-6 flex flex-row items-center gap-2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <p>No records found</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full my-6">
                            <thead>
                                <tr class="text-left bg-gray-100">
                                    <th class="p-2">Date</th>
                                    <th class="p-2">Status</th>
                                    <th class="p-2">Minecraft UUID</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($banAppeals as $banAppeal)
                                <tr>
                                    <td class="p-2">
                                        <a href="{{ route('front.appeal.show', $banAppeal) }}" class="text-blue-500">
                                            {{ $banAppeal->created_at?->utc() ?? "Unknown" }}
                                        </a>
                                    </td>
                                    <td class="p-2">{{ $banAppeal->status->humanReadable() }}</td>
                                    <td class="p-2">{{ $banAppeal->minecraft_uuid }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </main>
@endsection
