type InstagramCarouselSlide = {
    src: string
    alt: string
}

const instagramCarouselDialog = document.querySelector<HTMLDialogElement>('#instagram-carousel-dialog')
const instagramCarouselImage = document.querySelector<HTMLImageElement>('#instagram-carousel-image')
const instagramCarouselLoading = document.querySelector<HTMLElement>('#instagram-carousel-image-loading')
const instagramCarouselCount = document.querySelector<HTMLElement>('#instagram-carousel-count')
const instagramCarouselError = document.querySelector<HTMLElement>('#instagram-carousel-image-error')
const instagramCarouselPrevious = document.querySelector<HTMLButtonElement>('[data-instagram-carousel-previous]')
const instagramCarouselNext = document.querySelector<HTMLButtonElement>('[data-instagram-carousel-next]')
const instagramCarouselClose = document.querySelector<HTMLButtonElement>('[data-instagram-carousel-close]')
const instagramCarouselRetry = document.querySelector<HTMLButtonElement>('[data-instagram-carousel-retry]')
const instagramCarouselOverlay = document.querySelector<HTMLElement>('[data-instagram-carousel-overlay]')
const instagramCarouselPermalink = document.querySelector<HTMLAnchorElement>('#instagram-carousel-permalink')

if (
    instagramCarouselDialog
    && instagramCarouselImage
    && instagramCarouselLoading
    && instagramCarouselCount
    && instagramCarouselError
    && instagramCarouselPrevious
    && instagramCarouselNext
    && instagramCarouselClose
    && instagramCarouselRetry
    && instagramCarouselOverlay
    && instagramCarouselPermalink
) {
    let slides: InstagramCarouselSlide[] = []
    let currentSlide = 0
    let openButton: HTMLButtonElement | null = null
    let pointerStartX: number | null = null
    let openingThumbnailRect: DOMRect | null = null
    let openingAnimationPending = false
    let photoAnimation: Animation | null = null
    let overlayAnimation: Animation | null = null
    let isClosing = false

    const showSlide = (slideIndex: number, animateFromThumbnail = false): void => {
        if (isClosing) {
            return
        }

        if (!animateFromThumbnail) {
            openingThumbnailRect = null
            openingAnimationPending = false
            photoAnimation?.cancel()
            photoAnimation = null
        }

        const nextSlideIndex = (slideIndex + slides.length) % slides.length
        const slide = slides[nextSlideIndex]

        if (!slide) {
            return
        }

        currentSlide = nextSlideIndex
        instagramCarouselImage.alt = slide.alt
        instagramCarouselImage.hidden = true
        instagramCarouselLoading.hidden = false
        instagramCarouselCount.textContent = `Photo ${currentSlide + 1} of ${slides.length}`
        instagramCarouselError.hidden = true
        instagramCarouselImage.src = slide.src
    }

    const retryCurrentSlide = (): void => {
        const slide = slides[currentSlide]

        if (!slide || isClosing) {
            return
        }

        instagramCarouselImage.removeAttribute('src')
        instagramCarouselImage.alt = slide.alt
        instagramCarouselImage.hidden = true
        instagramCarouselLoading.hidden = false
        instagramCarouselError.hidden = true

        requestAnimationFrame(() => {
            if (!isClosing && slides[currentSlide] === slide) {
                instagramCarouselImage.src = slide.src
            }
        })
    }

    const animatePhotoIntoPlace = (): void => {
        const sourceRect = openingThumbnailRect

        if (
            !openingAnimationPending
            || !sourceRect
            || !instagramCarouselDialog.open
            || !instagramCarouselImage.complete
            || instagramCarouselImage.naturalWidth === 0
        ) {
            return
        }

        openingAnimationPending = false

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return
        }

        const targetRect = instagramCarouselImage.getBoundingClientRect()

        if (targetRect.width === 0 || targetRect.height === 0) {
            return
        }

        const scale = Math.min(
            sourceRect.width / targetRect.width,
            sourceRect.height / targetRect.height,
        )
        const translateX = sourceRect.left + sourceRect.width / 2 - (targetRect.left + targetRect.width / 2)
        const translateY = sourceRect.top + sourceRect.height / 2 - (targetRect.top + targetRect.height / 2)

        photoAnimation = instagramCarouselImage.animate(
            [
                { transform: `translate(${translateX}px, ${translateY}px) scale(${scale})`, opacity: 0.7 },
                { transform: 'translate(0, 0) scale(1)', opacity: 1 },
            ],
            { duration: 420, easing: 'cubic-bezier(0.22, 1, 0.36, 1)' },
        )
    }

    const animateOverlayIntoPlace = (): void => {
        overlayAnimation?.cancel()
        overlayAnimation = null
        instagramCarouselOverlay.classList.add('opacity-0')

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            instagramCarouselOverlay.classList.remove('opacity-0')

            return
        }

        overlayAnimation = instagramCarouselOverlay.animate(
            [{ opacity: 0 }, { opacity: 1 }],
            { duration: 420, easing: 'cubic-bezier(0.22, 1, 0.36, 1)', fill: 'forwards' },
        )
    }

    const closeCarousel = (): void => {
        if (!instagramCarouselDialog.open || isClosing) {
            return
        }

        openingAnimationPending = false
        photoAnimation?.cancel()
        photoAnimation = null

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            overlayAnimation?.cancel()
            overlayAnimation = null
            instagramCarouselDialog.close()

            return
        }

        const overlayOpacity = getComputedStyle(instagramCarouselOverlay).opacity
        overlayAnimation?.cancel()

        isClosing = true
        overlayAnimation = instagramCarouselOverlay.animate(
            [{ opacity: overlayOpacity }, { opacity: 0 }],
            { duration: 320, easing: 'cubic-bezier(0.4, 0, 1, 1)', fill: 'forwards' },
        )

        const closingAnimations = [overlayAnimation]
        const thumbnail = openButton?.querySelector('img')
        const thumbnailRect = thumbnail?.getBoundingClientRect()

        if (
            thumbnailRect
            && thumbnailRect.width > 0
            && thumbnailRect.height > 0
            && instagramCarouselImage.complete
            && instagramCarouselImage.naturalWidth > 0
        ) {
            const imageRect = instagramCarouselImage.getBoundingClientRect()

            if (imageRect.width > 0 && imageRect.height > 0) {
                const scale = Math.min(
                    thumbnailRect.width / imageRect.width,
                    thumbnailRect.height / imageRect.height,
                )
                const translateX = thumbnailRect.left + thumbnailRect.width / 2 - (imageRect.left + imageRect.width / 2)
                const translateY = thumbnailRect.top + thumbnailRect.height / 2 - (imageRect.top + imageRect.height / 2)

                photoAnimation = instagramCarouselImage.animate(
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

                if (unfinishedAnimations === 0 && isClosing && instagramCarouselDialog.open) {
                    instagramCarouselDialog.close()
                }
            }, { once: true })
        })
    }

    document.querySelectorAll<HTMLButtonElement>('[data-instagram-carousel-open]').forEach(button => {
        button.addEventListener('click', () => {
            const templateId = button.dataset.instagramCarouselTemplate
            const permalink = button.dataset.instagramCarouselPermalink
            const template = templateId ? document.getElementById(templateId) : null

            if (!(template instanceof HTMLTemplateElement) || !permalink) {
                console.error('The Instagram carousel data is missing.')

                return
            }

            slides = Array.from(template.content.querySelectorAll<HTMLElement>('[data-instagram-carousel-slide]'))
                .map(slide => ({
                    src: slide.dataset.imageSrc ?? '',
                    alt: slide.dataset.imageAlt ?? '',
                }))
                .filter((slide): slide is InstagramCarouselSlide => slide.src !== '')

            if (slides.length < 2) {
                console.error('The Instagram carousel does not contain multiple photos.')

                return
            }

            openButton = button
            openingThumbnailRect = button.querySelector('img')?.getBoundingClientRect() ?? null
            openingAnimationPending = true
            isClosing = false
            instagramCarouselPermalink.href = permalink
            showSlide(0, true)
            instagramCarouselDialog.showModal()
            animateOverlayIntoPlace()
            requestAnimationFrame(animatePhotoIntoPlace)
            instagramCarouselClose.focus()
        })
    })

    instagramCarouselPrevious.addEventListener('click', () => showSlide(currentSlide - 1))
    instagramCarouselNext.addEventListener('click', () => showSlide(currentSlide + 1))
    instagramCarouselClose.addEventListener('click', closeCarousel)
    instagramCarouselRetry.addEventListener('click', retryCurrentSlide)

    instagramCarouselDialog.addEventListener('click', event => {
        if (event.target === instagramCarouselDialog) {
            closeCarousel()
        }
    })

    instagramCarouselDialog.addEventListener('keydown', event => {
        if (event.key === 'ArrowLeft') {
            event.preventDefault()
            showSlide(currentSlide - 1)
        } else if (event.key === 'ArrowRight') {
            event.preventDefault()
            showSlide(currentSlide + 1)
        } else if (event.key === 'Escape') {
            event.preventDefault()
            closeCarousel()
        }
    })

    instagramCarouselDialog.addEventListener('cancel', event => {
        event.preventDefault()
        closeCarousel()
    })

    instagramCarouselDialog.addEventListener('close', () => {
        openingThumbnailRect = null
        openingAnimationPending = false
        photoAnimation?.cancel()
        photoAnimation = null
        overlayAnimation?.cancel()
        overlayAnimation = null
        instagramCarouselOverlay.classList.add('opacity-0')
        isClosing = false
        instagramCarouselImage.removeAttribute('src')
        instagramCarouselImage.hidden = true
        instagramCarouselLoading.hidden = true
        instagramCarouselError.hidden = true
        openButton?.focus()
        openButton = null
    })

    instagramCarouselImage.addEventListener('error', () => {
        openingThumbnailRect = null
        instagramCarouselImage.hidden = true
        instagramCarouselLoading.hidden = true
        instagramCarouselError.hidden = false
    })

    instagramCarouselImage.addEventListener('load', () => {
        instagramCarouselImage.hidden = false
        instagramCarouselLoading.hidden = true
        instagramCarouselError.hidden = true
        animatePhotoIntoPlace()
    })

    instagramCarouselImage.addEventListener('pointerdown', event => {
        if (event.pointerType === 'mouse') {
            return
        }

        pointerStartX = event.clientX
        instagramCarouselImage.setPointerCapture(event.pointerId)
    })

    instagramCarouselImage.addEventListener('pointerup', event => {
        if (pointerStartX === null) {
            return
        }

        const swipeDistance = event.clientX - pointerStartX
        pointerStartX = null

        if (Math.abs(swipeDistance) >= 48) {
            showSlide(currentSlide + (swipeDistance < 0 ? 1 : -1))
        }
    })

    instagramCarouselImage.addEventListener('pointercancel', () => {
        pointerStartX = null
    })
}
