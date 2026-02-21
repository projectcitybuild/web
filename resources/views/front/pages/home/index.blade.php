@extends('front.layouts.root-layout')

@section('title', 'Project City Build - Creative Minecraft Community')
@section('meta_title', 'Project City Build - Creative Minecraft Community')
@section('meta_description')
One of the world's longest-running Minecraft servers; we're a community of creative players and city builders @endsection

@section('body')
    <x-front::hero />

    <main>
        <div class="h-12"></div>

        <section class="bg-gray-50">
            <div class="max-w-screen-2xl mx-auto py-10 px-6 md:px-12 md:py-24 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="inline-flex flex-col justify-start">
                        <div class="bg-orange-500 p-4 my-6 rounded-md text-sm font-bold text-white">
                            Since October 2010
                        </div>
                        <div class="ml-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-l-transparent border-r-transparent border-t-orange-500 -mt-6"></div>
                    </div>

                    <div class="mt-2 text-6xl text-gray-800 font-bold leading-[1.2] tracking-tight">
                        Crafting Worlds.<br />
                        Crafting Connections.
                    </div>

                    <div class="mt-4 text-gray-500 text-md leading-loose pt-3">
                        <strong>For more than 15 years</strong>, our Minecraft community has been a place to dream big, build together, and share creations.
                        From sprawling cities to unique personal builds, we welcome all — come join the fun and make lasting friendships.
                    </div>

                    <div class="mt-8 flex flex-row gap-4">
                        <a href="#" class="text-sm text-blue-500 inline-flex flex-row items-center">
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m10 16 4-4-4-4"/>
                            </svg>
                            <span class="underline">Read Our History</span>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-md font-light text-gray-500 ml-6 mb-3">Getting Started</h3>

                    <div class="flex flex-col gap-4">
                        <article class="p-6 bg-gray-100 rounded-lg">
                            <h2 class="mb-2 text-lg tracking-tight text-gray-900 dark:text-white">
                                <a href="#" class="inline-flex items-center text-gray-600">
                                    Worlds
                                    <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </a>
                            </h2>
                            <p class="font-light text-sm text-gray-500 dark:text-gray-400">
                                Check out our different worlds and modes to play on
                            </p>
                        </article>
                        <article class="p-6 bg-gray-100 rounded-lg">
                            <h2 class="mb-2 text-lg tracking-tight text-gray-900 dark:text-white">
                                <a href="#" class="inline-flex items-center text-gray-600">
                                    Ranks
                                    <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </a>
                            </h2>
                            <p class="font-light text-sm text-gray-500 dark:text-gray-400">
                                Check out our two ladder system - trust and build ranks
                            </p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="max-w-screen-2xl mx-auto p-12">
                <h1 class="text-3xl font-bold text-center mx-auto">Latest Builds</h1>
                <p class="text-sm text-gray-500 text-center mt-4">Showcasing the awesome talent of members of our community.</p>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-12">
                    <div class="grid gap-4">
                        <div>
                            <img class="h-auto max-w-full rounded-base" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image.jpg" alt="">
                        </div>
                        <div>
                            <img class="h-auto max-w-full rounded-base" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-1.jpg" alt="">
                        </div>
                        <div>
                            <img class="h-auto max-w-full rounded-base" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-2.jpg" alt="">
                        </div>
                    </div>
                    <div class="grid gap-4">
                        <div>
                            <img class="h-auto max-w-full rounded-base" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-3.jpg" alt="">
                        </div>
                        <div>
                            <img class="h-auto max-w-full rounded-base" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-4.jpg" alt="">
                        </div>
                        <div>
                            <img class="h-auto max-w-full rounded-base" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-5.jpg" alt="">
                        </div>
                    </div>
                    <div class="grid gap-4">
                        <div>
                            <img class="h-auto max-w-full rounded-base" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-6.jpg" alt="">
                        </div>
                        <div>
                            <img class="h-auto max-w-full rounded-base" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-7.jpg" alt="">
                        </div>
                        <div>
                            <img class="h-auto max-w-full rounded-base" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-8.jpg" alt="">
                        </div>
                    </div>
                    <div class="grid gap-4">
                        <div>
                            <img class="h-auto max-w-full rounded-base" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-9.jpg" alt="">
                        </div>
                        <div>
                            <img class="h-auto max-w-full rounded-base" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-10.jpg" alt="">
                        </div>
                        <div>
                            <img class="h-auto max-w-full rounded-base" src="https://flowbite.s3.amazonaws.com/docs/gallery/masonry/image-11.jpg" alt="">
                        </div>
                    </div>
                </div>

                <div class="flex flex-row justify-center items-center gap-2 mt-12 text-gray-500 underline">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                        fill-rule="evenodd"
                        d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                        clip-rule="evenodd"
                        />
                    </svg>
                    <a href="https://www.instagram.com/projectcitybuild" target="_blank">View more on our Instagram page</a>
                </div>
            </div>
        </section>
    </main>

    @include('front.components.footer')
@endsection
