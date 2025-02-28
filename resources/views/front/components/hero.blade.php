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
                    <span class="font-bold underline">pcbmc.co</span>
                </a>

                <a href="{{ config('discord.invite_url') }}" class="text-sm font-semibold leading-6 text-indigo-300 flex gap-2">
                    <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.942 5.556a16.3 16.3 0 0 0-4.126-1.3 12.04 12.04 0 0 0-.529 1.1 15.175 15.175 0 0 0-4.573 0 11.586 11.586 0 0 0-.535-1.1 16.274 16.274 0 0 0-4.129 1.3 17.392 17.392 0 0 0-2.868 11.662 15.785 15.785 0 0 0 4.963 2.521c.41-.564.773-1.16 1.084-1.785a10.638 10.638 0 0 1-1.706-.83c.143-.106.283-.217.418-.331a11.664 11.664 0 0 0 10.118 0c.137.114.277.225.418.331-.544.328-1.116.606-1.71.832a12.58 12.58 0 0 0 1.084 1.785 16.46 16.46 0 0 0 5.064-2.595 17.286 17.286 0 0 0-2.973-11.59ZM8.678 14.813a1.94 1.94 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.918 1.918 0 0 1 1.8 2.047 1.929 1.929 0 0 1-1.8 2.045Zm6.644 0a1.94 1.94 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.919 1.919 0 0 1 1.8 2.047 1.93 1.93 0 0 1-1.8 2.045Z"/>
                    </svg>
                    Join our Discord <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </div>
</header>
