<header class="flex min-h-[40rem] flex-col bg-[linear-gradient(90deg,rgba(15,23,42,0.65),rgba(15,23,42,0.7)),url('/resources/images/hero.png')] bg-cover bg-center lg:min-h-screen sm:bg-[linear-gradient(90deg,rgba(15,23,42,0.18)_0%,rgba(15,23,42,0.84)_100%),url('/resources/images/hero.png')]">
    <x-front::navbar variant="clear" />

    <div class="mx-auto flex w-full max-w-screen-2xl grow items-center px-4 py-16 sm:px-8 lg:px-12">
        <div class="w-full max-w-3xl sm:ml-auto sm:text-right">
            <h1 class="mt-6 font-display text-6xl leading-[0.92] tracking-tight text-white sm:text-7xl md:text-8xl">
                We Build Stuff.<br class="hidden sm:block" /> Come Join Us!
            </h1>

            <p class="mt-6 max-w-2xl text-base leading-7 text-gray-200 sm:ml-auto sm:text-lg sm:leading-8">
                One of the <strong class="font-semibold text-white">world's longest-running</strong> Minecraft servers; we're a community of creative players and city builders.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('front.connect') }}" aria-label="Connect to pcbmc.co" class="inline-flex min-h-12 items-center justify-center gap-1 rounded-lg bg-amber-400 px-5 py-3 text-sm font-bold text-gray-950 shadow-lg shadow-black/20 transition-colors hover:bg-amber-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-300">
                    <span>Connect to</span>
                    <span class="underline underline-offset-4">pcbmc.co</span>
                    <span aria-hidden="true">→</span>
                </a>
                <a href="{{ config('discord.invite_url') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-lg border border-white/35 bg-white/10 px-5 py-3 text-sm font-semibold text-white backdrop-blur-sm transition-colors hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                    Join our Discord
                </a>
            </div>
        </div>
    </div>
</header>
