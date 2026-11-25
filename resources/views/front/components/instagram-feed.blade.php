@props(['feedConfigured', 'images'])

<section id="instagram" aria-labelledby="instagram-heading" class="bg-gray-50 py-16 dark:bg-gray-900/60 sm:py-20 lg:py-24">
    <div class="mx-auto max-w-screen-2xl px-4 sm:px-8">
        <div data-home-reveal class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-amber-700 dark:text-amber-400">Hot off the press</p>
            <h2 id="instagram-heading" class="mt-3 font-display text-5xl leading-none sm:text-6xl">By our community</h2>
            <a href="https://www.instagram.com/projectcitybuild" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center gap-1 font-semibold text-blue-700 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-200">
                Follow us on Instagram <span aria-hidden="true">→</span>
            </a>
        </div>

        @if (! $feedConfigured)
            <p class="mt-10 text-center text-gray-600 dark:text-gray-300">The Instagram feed is not configured yet.</p>
        @elseif ($images === null)
            <p class="mt-10 text-center text-gray-600 dark:text-gray-300">Instagram images are currently unavailable.</p>
        @elseif ($images === [])
            <p class="mt-10 text-center text-gray-600 dark:text-gray-300">No recent Instagram images are available.</p>
        @else
            <ul class="mt-10 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:mt-14 lg:grid-cols-4 lg:gap-3">
                @foreach ($images as $image)
                    <li data-home-reveal data-home-reveal-delay="{{ $loop->index % 4 * 60 }}" class="overflow-hidden rounded-md border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950">
                        @php($coverImage = $image['images'][0])

                        @if (count($image['images']) > 1)
                            <button
                                type="button"
                                data-instagram-carousel-open
                                data-instagram-carousel-template="instagram-carousel-slides-{{ $loop->index }}"
                                data-instagram-carousel-permalink="{{ $image['permalink'] }}"
                                aria-label="View all {{ count($image['images']) }} photos from this Instagram post"
                                class="group relative block w-full overflow-hidden focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500"
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

            <dialog
                id="instagram-carousel-dialog"
                aria-labelledby="instagram-carousel-heading"
                class="fixed inset-0 m-0 h-dvh max-h-dvh w-screen max-w-none overflow-hidden rounded-none border-0 bg-transparent p-0 text-white shadow-none backdrop:bg-transparent"
            >
                <div
                    data-instagram-carousel-overlay
                    aria-hidden="true"
                    class="pointer-events-none absolute inset-0 z-0 bg-gray-950/80 opacity-0 backdrop-blur-md"
                ></div>
                <div class="relative z-10 flex h-full min-h-0 flex-col">
                    <div class="flex shrink-0 items-center justify-between gap-4 border-b border-white/10 px-4 py-4 sm:px-6">
                        <div>
                            <p id="instagram-carousel-count" aria-live="polite" aria-atomic="true" class="mt-1 text-sm text-gray-300"></p>
                        </div>
                        <button
                            type="button"
                            data-instagram-carousel-close
                            aria-label="Close photo carousel"
                            class="flex size-10 shrink-0 items-center justify-center rounded-full text-2xl text-gray-200 transition hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-400"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="relative grid min-h-0 flex-1 grid-cols-1 items-center overflow-hidden bg-transparent sm:grid-cols-[auto_minmax(0,1fr)_auto] sm:gap-4 sm:p-4">
                        <button
                            type="button"
                            data-instagram-carousel-previous
                            aria-label="Previous photo"
                            class="absolute left-2 top-1/2 z-10 flex size-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-2xl text-white transition hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-400 disabled:cursor-not-allowed disabled:opacity-40 sm:static sm:size-12 sm:translate-y-0"
                        >
                            <span aria-hidden="true">&lsaquo;</span>
                        </button>
                        <div class="flex h-full w-full min-h-0 min-w-0 items-center justify-center">
                            <img
                                id="instagram-carousel-image"
                                alt=""
                                draggable="false"
                                class="h-full w-full max-h-full max-w-full touch-pan-y object-contain"
                            >
                            <p
                                id="instagram-carousel-image-error"
                                role="alert"
                                hidden
                                class="py-8 text-center text-sm text-gray-200"
                            >
                                This photo could not be loaded.
                            </p>
                        </div>
                        <button
                            type="button"
                            data-instagram-carousel-next
                            aria-label="Next photo"
                            class="absolute right-2 top-1/2 z-10 flex size-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-2xl text-white transition hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-400 disabled:cursor-not-allowed disabled:opacity-40 sm:static sm:size-12 sm:translate-y-0"
                        >
                            <span aria-hidden="true">&rsaquo;</span>
                        </button>
                    </div>

                    <div class="flex shrink-0 justify-end border-t border-white/10 px-4 py-4 sm:px-6">
                        <a
                            id="instagram-carousel-permalink"
                            href="#"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-semibold text-blue-300 hover:text-blue-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-400"
                        >
                            View post on Instagram <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </dialog>
        @endif
    </div>
</section>
