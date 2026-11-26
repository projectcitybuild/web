<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue'

type InstagramCarouselSlide = {
    src: string,
    alt: string,
}

const dialog = ref<HTMLDialogElement | null>(null)
const imageElement = ref<HTMLImageElement | null>(null)
const closeButton = ref<HTMLButtonElement | null>(null)
const overlay = ref<HTMLElement | null>(null)

const slides = ref<InstagramCarouselSlide[]>([])
const currentSlideIndex = ref(0)
const currentSlide = computed(() => slides.value[currentSlideIndex.value] ?? null)
const permalink = ref('')
const isLoading = ref(false)
const hasImageError = ref(false)
const imageAttempt = ref(0)

let openButton: HTMLButtonElement | null = null
let pointerStartX: number | null = null
let openingThumbnailRect: DOMRect | null = null
let openingAnimationPending = false
let photoAnimation: Animation | null = null
let overlayAnimation: Animation | null = null
let isClosing = false

function prefersReducedMotion(): boolean {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches
}

function showSlide(slideIndex: number, animateFromThumbnail = false): void {
    if (isClosing || slides.value.length === 0) {
        return
    }

    if (!animateFromThumbnail) {
        openingThumbnailRect = null
        openingAnimationPending = false
        photoAnimation?.cancel()
        photoAnimation = null
    }

    currentSlideIndex.value = (slideIndex + slides.value.length) % slides.value.length
    imageAttempt.value = 0
    isLoading.value = true
    hasImageError.value = false
    void nextTick(syncCurrentImageState)
}

function retryCurrentSlide(): void {
    if (!currentSlide.value || isClosing) {
        return
    }

    imageAttempt.value += 1
    isLoading.value = true
    hasImageError.value = false
    void nextTick(syncCurrentImageState)
}

function setImageLoaded(image: HTMLImageElement): void {
    if (image !== imageElement.value) {
        return
    }

    isLoading.value = false
    hasImageError.value = false
    void nextTick(() => window.requestAnimationFrame(animatePhotoIntoPlace))
}

function setImageError(image: HTMLImageElement): void {
    if (image !== imageElement.value) {
        return
    }

    openingThumbnailRect = null
    openingAnimationPending = false
    isLoading.value = false
    hasImageError.value = true
}

function syncCurrentImageState(): void {
    const image = imageElement.value

    if (!image?.complete) {
        return
    }

    if (image.naturalWidth > 0) {
        setImageLoaded(image)
    } else {
        setImageError(image)
    }
}

function animatePhotoIntoPlace(): void {
    const sourceRect = openingThumbnailRect
    const image = imageElement.value

    if (
        !openingAnimationPending
        || !sourceRect
        || !dialog.value?.open
        || !image?.complete
        || image.naturalWidth === 0
    ) {
        return
    }

    openingAnimationPending = false

    if (prefersReducedMotion()) {
        return
    }

    const targetRect = image.getBoundingClientRect()

    if (targetRect.width === 0 || targetRect.height === 0) {
        return
    }

    const scale = Math.min(
        sourceRect.width / targetRect.width,
        sourceRect.height / targetRect.height,
    )
    const translateX = sourceRect.left + sourceRect.width / 2 - (targetRect.left + targetRect.width / 2)
    const translateY = sourceRect.top + sourceRect.height / 2 - (targetRect.top + targetRect.height / 2)

    photoAnimation = image.animate(
        [
            { transform: `translate(${translateX}px, ${translateY}px) scale(${scale})`, opacity: 0.7 },
            { transform: 'translate(0, 0) scale(1)', opacity: 1 },
        ],
        { duration: 420, easing: 'cubic-bezier(0.22, 1, 0.36, 1)' },
    )
}

function animateOverlayIntoPlace(): void {
    const overlayElement = overlay.value

    if (!overlayElement) {
        return
    }

    overlayAnimation?.cancel()
    overlayAnimation = null
    overlayElement.classList.add('opacity-0')

    if (prefersReducedMotion()) {
        overlayElement.classList.remove('opacity-0')

        return
    }

    overlayAnimation = overlayElement.animate(
        [{ opacity: 0 }, { opacity: 1 }],
        { duration: 420, easing: 'cubic-bezier(0.22, 1, 0.36, 1)', fill: 'forwards' },
    )
}

function closeCarousel(): void {
    const dialogElement = dialog.value

    if (!dialogElement?.open || isClosing) {
        return
    }

    openingAnimationPending = false
    photoAnimation?.cancel()
    photoAnimation = null

    if (prefersReducedMotion()) {
        overlayAnimation?.cancel()
        overlayAnimation = null
        dialogElement.close()

        return
    }

    const overlayElement = overlay.value

    if (!overlayElement) {
        throw new Error('The Instagram carousel overlay is missing.')
    }

    const overlayOpacity = getComputedStyle(overlayElement).opacity
    overlayAnimation?.cancel()

    isClosing = true
    const closingOverlayAnimation = overlayElement.animate(
        [{ opacity: overlayOpacity }, { opacity: 0 }],
        { duration: 320, easing: 'cubic-bezier(0.4, 0, 1, 1)', fill: 'forwards' },
    )
    overlayAnimation = closingOverlayAnimation

    const closingAnimations = [closingOverlayAnimation]
    const thumbnail = openButton?.querySelector('img')
    const thumbnailRect = thumbnail?.getBoundingClientRect()
    const image = imageElement.value

    if (
        thumbnailRect
        && thumbnailRect.width > 0
        && thumbnailRect.height > 0
        && image
        && !isLoading.value
        && !hasImageError.value
        && image.complete
        && image.naturalWidth > 0
    ) {
        const imageRect = image.getBoundingClientRect()

        if (imageRect.width > 0 && imageRect.height > 0) {
            const scale = Math.min(
                thumbnailRect.width / imageRect.width,
                thumbnailRect.height / imageRect.height,
            )
            const translateX = thumbnailRect.left + thumbnailRect.width / 2 - (imageRect.left + imageRect.width / 2)
            const translateY = thumbnailRect.top + thumbnailRect.height / 2 - (imageRect.top + imageRect.height / 2)

            photoAnimation = image.animate(
                [
                    { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                    { transform: `translate(${translateX}px, ${translateY}px) scale(${scale})`, opacity: 0.7 },
                ],
                { duration: 320, easing: 'cubic-bezier(0.4, 0, 1, 1)' },
            )
            closingAnimations.push(photoAnimation)
        }
    }

    let unfinishedAnimations = closingAnimations.length

    closingAnimations.forEach(animation => {
        animation.addEventListener('finish', () => {
            unfinishedAnimations -= 1

            if (unfinishedAnimations === 0 && isClosing && dialogElement.open) {
                dialogElement.close()
            }
        }, { once: true })
    })
}

async function openCarousel(button: HTMLButtonElement): Promise<void> {
    const templateId = button.dataset.instagramCarouselTemplate
    const postPermalink = button.dataset.instagramCarouselPermalink
    const template = templateId ? document.getElementById(templateId) : null

    if (!(template instanceof HTMLTemplateElement) || !postPermalink) {
        console.error('The Instagram carousel data is missing.')

        return
    }

    const nextSlides = Array.from(template.content.querySelectorAll<HTMLElement>('[data-instagram-carousel-slide]'))
        .map(slide => ({
            src: slide.dataset.imageSrc ?? '',
            alt: slide.dataset.imageAlt ?? '',
        }))
        .filter(slide => slide.src !== '')

    if (nextSlides.length < 2) {
        console.error('The Instagram carousel does not contain multiple photos.')

        return
    }

    const dialogElement = dialog.value
    const closeButtonElement = closeButton.value

    if (!dialogElement || !closeButtonElement) {
        throw new Error('The Instagram carousel dialog is not available.')
    }

    openButton = button
    openingThumbnailRect = button.querySelector('img')?.getBoundingClientRect() ?? null
    openingAnimationPending = true
    isClosing = false
    slides.value = nextSlides
    currentSlideIndex.value = 0
    imageAttempt.value = 0
    permalink.value = postPermalink
    isLoading.value = true
    hasImageError.value = false

    dialogElement.showModal()
    animateOverlayIntoPlace()
    await nextTick()
    syncCurrentImageState()
    window.requestAnimationFrame(animatePhotoIntoPlace)
    closeButtonElement.focus()
}

function handleOpenClick(event: MouseEvent): void {
    if (!(event.target instanceof Element)) {
        return
    }

    const button = event.target.closest<HTMLButtonElement>('[data-instagram-carousel-open]')

    if (button) {
        void openCarousel(button)
    }
}

function handleImageLoad(event: Event): void {
    if (!(event.currentTarget instanceof HTMLImageElement)) {
        return
    }

    setImageLoaded(event.currentTarget)
}

function handleImageError(event: Event): void {
    if (!(event.currentTarget instanceof HTMLImageElement)) {
        return
    }

    setImageError(event.currentTarget)
}

function handleDialogClick(event: MouseEvent): void {
    if (event.target === dialog.value) {
        closeCarousel()
    }
}

function handleKeydown(event: KeyboardEvent): void {
    if (event.key === 'ArrowLeft') {
        event.preventDefault()
        showSlide(currentSlideIndex.value - 1)
    } else if (event.key === 'ArrowRight') {
        event.preventDefault()
        showSlide(currentSlideIndex.value + 1)
    } else if (event.key === 'Escape') {
        event.preventDefault()
        closeCarousel()
    }
}

function handlePointerDown(event: PointerEvent): void {
    if (event.pointerType === 'mouse') {
        return
    }

    pointerStartX = event.clientX

    if (event.currentTarget instanceof HTMLImageElement) {
        event.currentTarget.setPointerCapture(event.pointerId)
    }
}

function handlePointerUp(event: PointerEvent): void {
    if (pointerStartX === null) {
        return
    }

    const swipeDistance = event.clientX - pointerStartX
    pointerStartX = null

    if (Math.abs(swipeDistance) >= 48) {
        showSlide(currentSlideIndex.value + (swipeDistance < 0 ? 1 : -1))
    }
}

function handleDialogClose(): void {
    openingThumbnailRect = null
    openingAnimationPending = false
    photoAnimation?.cancel()
    photoAnimation = null
    overlayAnimation?.cancel()
    overlayAnimation = null
    overlay.value?.classList.add('opacity-0')
    isClosing = false
    isLoading.value = false
    hasImageError.value = false
    slides.value = []
    currentSlideIndex.value = 0
    imageAttempt.value = 0
    permalink.value = ''
    pointerStartX = null
    openButton?.focus()
    openButton = null
}

onMounted(() => {
    document.addEventListener('click', handleOpenClick)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleOpenClick)
    photoAnimation?.cancel()
    overlayAnimation?.cancel()
})
</script>

<template>
    <dialog
        ref="dialog"
        aria-labelledby="instagram-carousel-heading"
        class="fixed inset-0 m-0 h-dvh max-h-dvh w-screen max-w-none overflow-hidden rounded-none border-0 bg-transparent p-0 text-white shadow-none backdrop:bg-transparent"
        @click="handleDialogClick"
        @keydown="handleKeydown"
        @cancel.prevent="closeCarousel"
        @close="handleDialogClose"
    >
        <div
            ref="overlay"
            aria-hidden="true"
            class="pointer-events-none absolute inset-0 z-0 bg-gray-950/80 opacity-0 backdrop-blur-md"
        ></div>
        <div class="pointer-events-none relative z-10 flex h-full min-h-0 flex-col">
            <div class="flex shrink-0 items-center justify-between gap-4 border-b border-white/10 px-4 py-4 sm:px-6">
                <div>
                    <h2 id="instagram-carousel-heading" class="sr-only">Instagram photo carousel</h2>
                    <p aria-live="polite" aria-atomic="true" class="mt-1 text-sm text-gray-300">
                        {{ currentSlide ? `Photo ${currentSlideIndex + 1} of ${slides.length}` : '' }}
                    </p>
                </div>
                <button
                    ref="closeButton"
                    type="button"
                    aria-label="Close photo carousel"
                    class="pointer-events-auto flex size-10 shrink-0 items-center justify-center rounded-full text-2xl text-gray-200 transition hover:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-400"
                    @click="closeCarousel"
                >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="relative grid min-h-0 flex-1 grid-cols-1 items-center overflow-hidden bg-transparent sm:grid-cols-[auto_minmax(0,1fr)_auto] sm:gap-4 sm:p-4">
                <button
                    type="button"
                    aria-label="Previous photo"
                    class="pointer-events-auto absolute left-2 top-1/2 z-10 flex size-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-2xl text-white transition hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-400 disabled:cursor-not-allowed disabled:opacity-40 sm:static sm:size-12 sm:translate-y-0"
                    @click="showSlide(currentSlideIndex - 1)"
                >
                    <span aria-hidden="true">&lsaquo;</span>
                </button>
                <div class="pointer-events-none relative flex h-full w-full min-h-0 min-w-0 items-center justify-center">
                    <img
                        v-if="currentSlide"
                        :key="`${currentSlideIndex}-${imageAttempt}`"
                        ref="imageElement"
                        :src="currentSlide.src"
                        :alt="currentSlide.alt"
                        draggable="false"
                        class="pointer-events-auto max-h-full max-w-full touch-pan-y"
                        :class="{ invisible: isLoading || hasImageError }"
                        @load="handleImageLoad"
                        @error="handleImageError"
                        @pointerdown="handlePointerDown"
                        @pointerup="handlePointerUp"
                        @pointercancel="pointerStartX = null"
                    >
                    <div
                        v-if="isLoading"
                        role="status"
                        class="absolute inset-0 flex items-center justify-center bg-gray-900/70"
                    >
                        <span class="flex flex-col items-center gap-3 text-sm text-gray-200">
                            <span aria-hidden="true" class="size-10 animate-spin rounded-full border-4 border-white/30 border-t-amber-400 motion-reduce:animate-none"></span>
                            Loading photo...
                        </span>
                    </div>
                    <div
                        v-if="hasImageError"
                        role="alert"
                        class="flex flex-col items-center gap-3 py-8 text-center text-sm text-gray-200"
                    >
                        <p>This photo could not be loaded.</p>
                        <button
                            type="button"
                            class="pointer-events-auto rounded-md px-3 py-2 font-semibold text-blue-300 underline underline-offset-4 hover:text-blue-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-400"
                            @click="retryCurrentSlide"
                        >
                            Try loading the photo again
                        </button>
                    </div>
                </div>
                <button
                    type="button"
                    aria-label="Next photo"
                    class="pointer-events-auto absolute right-2 top-1/2 z-10 flex size-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-2xl text-white transition hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-400 disabled:cursor-not-allowed disabled:opacity-40 sm:static sm:size-12 sm:translate-y-0"
                    @click="showSlide(currentSlideIndex + 1)"
                >
                    <span aria-hidden="true">&rsaquo;</span>
                </button>
            </div>

            <div class="flex shrink-0 justify-end border-t border-white/10 px-4 py-4 sm:px-6">
                <a
                    :href="permalink || '#'"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="pointer-events-auto font-semibold text-blue-300 hover:text-blue-200 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-400"
                >
                    View post on Instagram <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </dialog>
</template>
