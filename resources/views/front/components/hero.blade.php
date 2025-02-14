<header class="
    h-screen
    bg-[linear-gradient(to_right,hsla(0,0%,0%,0.25),hsla(0,21%,7%,90%)),url('/resources/images/hero.png')]
    bg-cover bg-no-repeat bg-center
    flex flex-col
">
    <x-front::navbar variant="clear" />

    <div class="flex flex-grow items-center justify-end">
        <div class="visible:m flex-grow"></div>

        <div class="sm:text-right py-32 px-4 sm:pr-24">
            <h1 class="text-7xl font-display sm:text-8xl lg:text-8xl text-gray-50 tracking-tight">
                We Build Stuff.<br />Come Join Us!
            </h1>

            <p class="mt-6 text-lg font-body leading-8 text-gray-300 tracking-tight">
                One of the <strong class="text-gray-50">world's longest-running</strong> Minecraft servers; we're a <br />
                community of creative players and city builders
            </p>

            <div class="
                mt-10 gap-x-8 gap-y-4 flex
                flex-col items-start justify-center
                sm:flex-row sm:items-center sm:justify-end
            ">
                <a href="javascript:void(0)" data-server-address="pcbmc.co" class="
                    text-sm sm:text-lg text-gray-900
                    bg-amber-500 rounded-md shadow-lg
                    hover:bg-amber-400
                    px-6 py-4
                    flex flex-row items-center
                    space-x-1
                ">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                    </svg>
                    <span>Connect to</span>
                    <span class="font-bold">
                        <i class="fas fa-copy"></i> <u>pcbmc.co</u>
                    </span>
                </a>

                <a href="{{ config('discord.invite_url') }}" class="text-sm font-semibold leading-6 text-indigo-300">
                    <i class="fab fa-discord"></i>
                    Join our Discord <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </div>
</header>
