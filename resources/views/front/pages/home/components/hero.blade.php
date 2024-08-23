<section class="
    h-screen
    bg-[linear-gradient(to_right,hsla(0,0%,0%,0.25),hsla(0,21%,7%,90%)),url('/resources/images/hero.png')]
    bg-cover bg-no-repeat bg-center
    flex flex-col items-stretch
">
    <nav class="
    `   flex items-center justify-between
        p-6 lg:px-8
        border-b border-white border-opacity-30
    " aria-label="Global">
        <div class="flex lg:flex-auto lg:gap-x-24">
            <a href="#" class="-m-1.5 p-1.5">
                <span class="sr-only">Project City Build</span>
                <img class="h-6 w-auto" srcset="{{ Vite::asset('resources/images/logo-1x.png') }},
                         {{ Vite::asset('resources/images/logo-2x.png') }} 2x"
                     src="{{ Vite::asset('resources/images/logo-1x.png') }}"
                     alt="Project City Build"
                />
            </a>
            <div class="hidden lg:flex lg:gap-x-12">
                <a href="#" class="text-sm font-semibold leading-6 text-gray-50">Portal</a>
                <a href="#" class="text-sm font-semibold leading-6 text-gray-50">Live Maps</a>
                <a href="#" class="text-sm font-semibold leading-6 text-gray-50">Vote For Us</a>
                <a href="#" class="text-sm font-semibold leading-6 text-gray-50">Donate</a>
                <a href="#" class="text-sm font-semibold leading-6 text-gray-50">More</a>
            </div>
        </div>
        <div class="flex lg:hidden">
            <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
                <span class="sr-only">Open main menu</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>
        <div class="hidden lg:flex lg:flex-1 lg:items-center lg:justify-end gap-x-6">
            <a href="#" class="text-sm font-semibold leading-6 text-gray-50">Log in <span aria-hidden="true">&rarr;</span></a>
        </div>
    </nav>

    <header class="absolute inset-x-0 top-0 z-50">
        <!-- Mobile menu, show/hide based on menu open state. -->
        <div class="lg:hidden" role="dialog" aria-modal="true">
            <!-- Background backdrop, show/hide based on slide-over state. -->
            <div class="fixed inset-0 z-50"></div>
            <div class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                <div class="flex items-center justify-between">
                    <a href="#" class="-m-1.5 p-1.5">
                        <span class="sr-only">Project City Build</span>
                        <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="">
                    </a>
                    <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-2 py-6">
                            <a href="#" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Product</a>
                            <a href="#" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Features</a>
                            <a href="#" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Marketplace</a>
                            <a href="#" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Company</a>
                        </div>
                        <div class="py-6">
                            <a href="#" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-gray-900 hover:bg-gray-50">Log in</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex flex-auto items-center justify-end">
        <div class="visible:m flex-grow"></div>

        <div class="
            sm:text-right
            py-32 px-4 sm:pr-24
        ">
            <h1 class="text-7xl font-display sm:text-8xl text-gray-50 tracking-tight">
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
                ">
                    Connect to
                    <span class="font-bold">
                    <i class="fas fa-copy"></i> <u>pcbmc.co</u>
                </span>
                </a>
                <a href="https://discord.gg/3NYaUeScDX" class="text-sm font-semibold leading-6 text-indigo-300">
                    <i class="fab fa-discord"></i>
                    Join our Discord <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </div>
</section>
