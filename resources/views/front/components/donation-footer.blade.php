<section class="bg-[#3B3B3D] py-16">
    <div class="max-w-screen-2xl mx-auto px-6 md:px-12 flex flex-col md:flex-row gap-8 lg:gap-16">
        <div class="max-w-[400px]">
            <h1 class="font-display text-4xl text-white">Help Keep Us Online</h1>

            <div class="mt-6 text-white leading-loose text-sm">
                Donations are the only way to keep our server running.<br />
                Donors receive perks such as flying, colored names and <a href="{{ route('front.donate') }}">much more</a>
            </div>

            <x-front::button
                type="submit"
                variant="filled"
                scheme="primary"
                href="{{ route('front.donate') }}"
                class="mt-8"
            >
                Donate
                </x-button>
        </div>

        <div class="grow">
            <x-donation-bar />

            <div class="space-y-4 max-w-[326px] mt-8">
                <dl class="flex items-center justify-between gap-2 text-sm">
                    <dt class="text-gray-400">
                        Days Remaining
                    </dt>
                    <dd class="text-gray-50">
                        {{ $donations['remaining_days'] }}
                    </dd>
                </dl>
                <dl class="flex items-center justify-between gap-2 text-sm">
                    <dt class="text-gray-400">
                        Funds Still Required
                    </dt>
                    <dd class="text-gray-50">
                        ${{ $donations['still_required'] }}
                    </dd>
                </dl>
                <dl class="flex items-center justify-between gap-2 text-sm">
                    <dt class="text-gray-400">
                        Raised Last Year
                    </dt>
                    <dd class="text-gray-50">
                        ${{ $donations['raised_last_year'] }}
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</section>
