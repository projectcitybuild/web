<div class="space-y-5 rounded-lg bg-gray-50 p-6 dark:bg-gray-700">
    <h3 class="text-base font-medium text-gray-700 hover:no-underline dark:text-gray-50">
        Help Keep Us Online
    </h3>

    <p class="text-sm text-gray-500 leading-relaxed">
        Donations are the only way to keep our server running.
        Donors receive perks such as flying, colored names and <a href="{{ route('front.donate') }}" class="underline">much more</a>.
    </p>

    <x-donation-bar />

    <hr class="border-gray-200 dark:border-gray-600" />

    <div class="space-y-4 mt-8">
        <dl class="flex items-center justify-between gap-2 text-xs">
            <dt class="text-gray-400">
                Days Remaining
            </dt>
            <dd class="text-gray-500">
                {{ $remaining_days }}
            </dd>
        </dl>
        <dl class="flex items-center justify-between gap-2 text-xs">
            <dt class="text-gray-400">
                Funds Still Required
            </dt>
            <dd class="text-gray-500">
                ${{ number_format($stats->raisedThisYear, 2) }} USD
            </dd>
        </dl>
        <dl class="flex items-center justify-between gap-2 text-xs">
            <dt class="text-gray-400">
                Raised Last Year
            </dt>
            <dd class="text-gray-500">
                ${{ number_format($stats->raisedLastYear, 2) }} USD
            </dd>
        </dl>
    </div>

    <hr class="border-gray-200 dark:border-gray-600" />

    <div class="space-y-4 sm:flex sm:space-y-0">
        <x-front::button
            type="submit"
            variant="filled"
            scheme="secondary"
            href="{{ route('front.donate') }}"
            class="w-full"
        >
            <svg class="w-6 h-6 text-gray-50 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M6 14h2m3 0h5M3 7v10a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Z"/>
            </svg>
            Donate
        </x-button>
    </div>
</div>
