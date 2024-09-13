@extends('front.root-layout')

@section('title', 'Donations')
@section('description', '')

@section('body')
    @include('front.pages.account.account-navbar')

    <main
        class="
            flex flex-col max-w-screen-xl
            md:flex-row md:mx-auto md:mt-8
        "
    >
        <div class="rounded-lg bg-white flex-grow p-6 m-2">
            <section>
                <h1 class="text-2xl font-bold mb-3">Your Donations</h1>

                <p>
                    A record of all your contributions to help keep us running
                </p>
            </section>

            @if($donations->count() == 0)
                <div class="rounded-md border-2 border-gray-100 my-6 p-6 flex flex-row items-center gap-2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                    </svg>
                    <p>You have not made any donations</p>
                </div>
            @else
                <table class="table table--first-col-padded table--striped">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($donations as $donation)
                        <tr>
                            <td>{{ $donation->created_at?->toFormattedDateString() ?? "Unknown" }}</td>
                            <td>${{ number_format($donation->amount, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            <hr class="my-6" />

            <section>
                <h1 class="text-2xl font-bold mb-3">Your Perks</h1>

                <p class="settings__description">Note: Subscriptions will auto-renew their associated perk prior to its expiry date</p>
            </section>

            @if($donationPerks->count() == 0)
                <div class="rounded-md border-2 border-gray-100 my-6 p-6 flex flex-row items-center gap-2 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v16.5M2.25 12h19.5M6.375 17.25a4.875 4.875 0 0 0 4.875-4.875V12m6.375 5.25a4.875 4.875 0 0 1-4.875-4.875V12m-9 8.25h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v13.5a1.5 1.5 0 0 0 1.5 1.5Zm12.621-9.44c-1.409 1.41-4.242 1.061-4.242 1.061s-.349-2.833 1.06-4.242a2.25 2.25 0 0 1 3.182 3.182ZM10.773 7.63c1.409 1.409 1.06 4.242 1.06 4.242S9 12.22 7.592 10.811a2.25 2.25 0 1 1 3.182-3.182Z" />
                    </svg>
                    <p>You do not have any perks</p>
                </div>
            @else
                <table class="table table--first-col-padded table--striped">
                    <thead>
                    <tr>
                        <th>Perk</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>Expiry Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($donationPerks as $perk)
                        <tr>
                            <td>
                                @if($perk->donationTier)
                                    {{ $perk->donationTier->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $perk->isActive() ? 'Active' : 'Expired' }}</td>
                            <td>{{ $perk->donation->created_at?->toFormattedDateString() ?? "Unknown" }}</td>
                            <td>
                                @if ($perk->expires_at !== null && $perk->isActive())
                                    {{ $perk->expires_at->toFormattedDateString() }}
                                    ({{ now()->diff($perk->expires_at)->days }} days remaining)
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </main>
@endsection
