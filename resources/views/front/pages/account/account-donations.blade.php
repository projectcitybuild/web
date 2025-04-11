@extends('front.layouts.root-layout')

@section('title', 'Donations')
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
                <h1 class="text-2xl font-bold mb-3">Your Donations</h1>

                <p class="text-gray-500">A record of all your contributions to help keep us running</p>

                @if($donations->count() == 0)
                    <div class="rounded-md border-2 border-gray-100 my-6 p-6 flex flex-row items-center gap-2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                        </svg>
                        <p>You have not made any donations</p>
                    </div>
                @else
                    <table class="w-full my-6">
                        <thead>
                            <tr class="text-left bg-gray-100">
                                <th class="p-2">Amount</th>
                                <th class="p-2">Donation Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($donations as $donation)
                            <tr>
                                <td class="p-2">{{ $donation->formattedPaidAmount() }}</td>
                                <td class="p-2">{{ $donation->created_at?->utc() ?? "Unknown" }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </section>

            <hr class="my-6" />

            <section>
                <h1 class="text-2xl font-bold mb-3">Your Perks</h1>

                <p class="text-gray-500">Benefits applied to your account</p>

                @if($donationPerks->count() == 0)
                    <div class="rounded-md border-2 border-gray-100 my-6 p-6 flex flex-row items-center gap-2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M2.25 12h19.5M6.375 17.25a4.875 4.875 0 0 0 4.875-4.875V12m6.375 5.25a4.875 4.875 0 0 1-4.875-4.875V12m-9 8.25h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v13.5a1.5 1.5 0 0 0 1.5 1.5Zm12.621-9.44c-1.409 1.41-4.242 1.061-4.242 1.061s-.349-2.833 1.06-4.242a2.25 2.25 0 0 1 3.182 3.182ZM10.773 7.63c1.409 1.409 1.06 4.242 1.06 4.242S9 12.22 7.592 10.811a2.25 2.25 0 1 1 3.182-3.182Z" />
                        </svg>
                        <p>You do not have any perks</p>
                    </div>
                @else
                    <table class="w-full my-6">
                        <thead>
                            <tr class="text-left bg-gray-100">
                                <th class="p-2">Perk</th>
                                <th class="p-2">Status</th>
                                <th class="p-2">Start Date</th>
                                <th class="p-2">End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($donationPerks as $perk)
                            <tr>
                                <td class="p-2">
                                    @if($perk->donationTier)
                                        {{ $perk->donationTier->name }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-2">{{ $perk->isActive() ? 'Active' : 'Expired' }}</td>
                                <td class="p-2">{{ $perk->donation->created_at?->toFormattedDateString() ?? "Unknown" }}</td>
                                <td class="p-2">
                                    @if ($perk->expires_at !== null && $perk->isActive())
                                        {{ $perk->expires_at->utc() }} UTC
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif

                <p class="text-gray-500 text-xs">Note: End date will automatically be extended if part of an active subscription</p>
            </section>
        </div>
    </main>
@endsection
