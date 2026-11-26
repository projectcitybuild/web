@props(['feedConfigured', 'images'])

<section id="instagram" aria-labelledby="instagram-heading" class="bg-gray-950 py-16 sm:py-20 lg:py-24">
    <div class="mx-auto max-w-screen-2xl px-4 sm:px-8">
        <div data-home-reveal class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-amber-400">Hot off the press</p>
            <h2 id="instagram-heading" class="mt-3 font-display text-5xl leading-none sm:text-6xl">By our community</h2>
            <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center gap-1 font-semibold text-blue-300 hover:text-blue-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-brand-instagram">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 7a4 4 0 0 1 4 -4h10a4 4 0 0 1 4 4v10a4 4 0 0 1 -4 4h-10a4 4 0 0 1 -4 -4l0 -10" />
                    <path d="M8.5 12a3.5 3.5 0 1 0 7 0a3.5 3.5 0 0 0 -7 0" />
                    <path d="M17 7v.01" />
                </svg>
                Follow us on Instagram
            </a>
        </div>

        @if (! $feedConfigured)
            <p class="mt-10 text-center text-gray-300">The Instagram feed is not configured yet.</p>
        @elseif ($images === null)
            <p class="mt-10 text-center text-gray-300">Instagram images are currently unavailable.</p>
        @elseif ($images === [])
            <p class="mt-10 text-center text-gray-300">No recent Instagram images are available.</p>
        @else
            <ul class="mt-10 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:mt-14 lg:grid-cols-4 lg:gap-3">
                @foreach ($images as $image)
                    <li data-home-reveal data-home-reveal-delay="{{ $loop->index % 4 * 60 }}" class="overflow-hidden rounded-md border border-gray-800 bg-gray-950">
                        @php($coverImage = $image['images'][0])

                        @if (count($image['images']) > 1)
                            <button
                                type="button"
                                data-instagram-carousel-open
                                data-instagram-carousel-template="instagram-carousel-slides-{{ $loop->index }}"
                                data-instagram-carousel-permalink="{{ $image['permalink'] }}"
                                aria-label="View all {{ count($image['images']) }} photos from this Instagram post"
                                class="group relative block w-full overflow-hidden focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500 cursor-pointer"
                            >
                                <img
                                    src="{{ route('front.instagram.image', ['imageKey' => $coverImage['imageKey']]) }}"
                                    alt="{{ $coverImage['altText'] }}"
                                    loading="lazy"
                                    decoding="async"
                                    class="aspect-square w-full object-cover transition-transform duration-300 group-hover:scale-105 motion-reduce:transition-none"
                                >
                                <span aria-hidden="true" class="absolute right-3 top-3 rounded-full bg-gray-950/80 px-3 py-1 text-xs font-semibold text-white backdrop-blur">
                                    {{ count($image['images']) }} photos
                                </span>
                            </button>

                            <template id="instagram-carousel-slides-{{ $loop->index }}">
                                @foreach ($image['images'] as $slide)
                                    <span
                                        data-instagram-carousel-slide
                                        data-image-src="{{ route('front.instagram.image', ['imageKey' => $slide['imageKey']]) }}"
                                        data-image-alt="{{ $slide['altText'] }}"
                                    ></span>
                                @endforeach
                            </template>
                        @else
                            <a href="{{ $image['permalink'] }}" target="_blank" rel="noopener noreferrer" class="block overflow-hidden focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500">
                                <img
                                    src="{{ route('front.instagram.image', ['imageKey' => $coverImage['imageKey']]) }}"
                                    alt="{{ $coverImage['altText'] }}"
                                    loading="lazy"
                                    decoding="async"
                                    class="aspect-square w-full object-cover transition-transform duration-300 hover:scale-105 motion-reduce:transition-none"
                                >
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
            <div id="instagram-carousel-app"></div>
        @endif
    </div>
</section>
