// Inform Vite where static assets are located
import.meta.glob([
    '../../images/**',
]);

import 'flowbite';
import './player-report-form'

type ThemePreference = 'light' | 'dark' | 'system'

const themeStorageKey = 'admin-theme'
const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)')
const themeToggles = document.querySelectorAll<HTMLButtonElement>('[data-theme-toggle]')

function getThemePreference(): ThemePreference {
    const storedTheme = window.localStorage.getItem(themeStorageKey)

    return storedTheme === 'light' || storedTheme === 'dark' || storedTheme === 'system'
        ? storedTheme
        : 'system'
}

function applyTheme(preference: ThemePreference): void {
    const isDark = preference === 'dark' || (preference === 'system' && prefersDarkScheme.matches)

    document.documentElement.classList.toggle('dark', isDark)

    const nextTheme: Record<ThemePreference, ThemePreference> = {
        light: 'dark',
        dark: 'system',
        system: 'light',
    }

    themeToggles.forEach(button => {
        const label = `Theme: ${preference === 'system' ? 'automatic' : preference}. Switch to ${nextTheme[preference]} theme`

        button.setAttribute('aria-label', label)
        button.setAttribute('title', label)

        button.querySelectorAll('[data-theme-icon]').forEach(icon => {
            icon.classList.toggle('hidden', icon.getAttribute('data-theme-icon') !== preference)
        })
    })
}

function setThemePreference(preference: ThemePreference): void {
    window.localStorage.setItem(themeStorageKey, preference)
    applyTheme(preference)
}

themeToggles.forEach(button => {
    button.addEventListener('click', () => {
        const nextTheme: Record<ThemePreference, ThemePreference> = {
            light: 'dark',
            dark: 'system',
            system: 'light',
        }

        setThemePreference(nextTheme[getThemePreference()])
    })
})

window.addEventListener('storage', event => {
    if (event.key !== themeStorageKey && event.key !== null) {
        return
    }

    applyTheme(getThemePreference())
})

prefersDarkScheme.addEventListener('change', () => {
    if (getThemePreference() === 'system') {
        applyTheme('system')
    }
})

applyTheme(getThemePreference())

const stickyNavbars = document.querySelectorAll<HTMLElement>('[data-sticky-navbar]')

stickyNavbars.forEach(navbar => {
    const placeholder = document.createElement('div')
    placeholder.setAttribute('aria-hidden', 'true')
    placeholder.style.height = '0px'
    navbar.before(placeholder)

    let isPinned = false
    let placeholderHeight = 0

    const updateNavbar = () => {
        const navbarTop = placeholder.getBoundingClientRect().top + window.scrollY
        const shouldPin = window.scrollY > navbarTop

        if (shouldPin !== isPinned) {
            isPinned = shouldPin
            navbar.classList.toggle('is-pinned', isPinned)
        }

        if (isPinned && navbar.offsetHeight !== placeholderHeight) {
            placeholderHeight = navbar.offsetHeight
            placeholder.style.height = `${placeholderHeight}px`
        } else if (!isPinned && placeholderHeight !== 0) {
            placeholderHeight = 0
            placeholder.style.height = '0px'
        }

        if (navbar.dataset.variant === 'clear') {
            navbar.style.setProperty('--navbar-background-opacity', Math.min(window.scrollY / 120, 0.95).toString())
        }
    }

    window.addEventListener('scroll', updateNavbar, { passive: true })
    window.addEventListener('resize', updateNavbar)
    updateNavbar()
})

const homeRevealElements = document.querySelectorAll<HTMLElement>('[data-home-reveal]')

if (homeRevealElements.length > 0 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
    const homeRevealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible')
                observer.unobserve(entry.target)
            }
        })
    }, {
        rootMargin: '0px 0px -48px 0px',
        threshold: 0.12,
    })

    homeRevealElements.forEach(element => {
        const delay = Number.parseInt(element.dataset.homeRevealDelay ?? '', 10)

        element.classList.add('home-reveal')

        if (Number.isFinite(delay)) {
            element.style.setProperty('--home-reveal-delay', `${delay}ms`)
        }

        homeRevealObserver.observe(element)
    })
}

const copyAddressButton = document.querySelector<HTMLButtonElement>('button[data-server-address]')
const copyAddressStatus = document.querySelector<HTMLElement>('[data-copy-address-status]')
const copyAddressLabel = copyAddressButton?.querySelector<HTMLElement>('[data-copy-address-label]')

if (copyAddressButton && copyAddressStatus && copyAddressLabel) {
    copyAddressButton.addEventListener('click', async () => {
        const serverAddress = copyAddressButton.dataset.serverAddress

        if (!serverAddress) {
            console.error('The Minecraft server address is missing from the copy button.')
            copyAddressStatus.textContent = 'The server address is unavailable. Please copy pcbmc.co manually.'

            return
        }

        try {
            await navigator.clipboard.writeText(serverAddress)
            copyAddressLabel.textContent = 'Copied!'
            copyAddressStatus.textContent = 'Address copied. Paste it into Minecraft Java Edition to connect.'

            window.setTimeout(() => {
                copyAddressLabel.textContent = 'Copy address'
            }, 2000)
        } catch (error) {
            console.error('Unable to copy the Minecraft server address.', error)
            copyAddressStatus.textContent = `Clipboard access failed. Please copy ${serverAddress} manually.`
        }
    })
}

type InstagramCarouselSlide = {
    src: string
    alt: string
}

const instagramCarouselDialog = document.querySelector<HTMLDialogElement>('#instagram-carousel-dialog')
const instagramCarouselImage = document.querySelector<HTMLImageElement>('#instagram-carousel-image')
const instagramCarouselCount = document.querySelector<HTMLElement>('#instagram-carousel-count')
const instagramCarouselError = document.querySelector<HTMLElement>('#instagram-carousel-image-error')
const instagramCarouselPrevious = document.querySelector<HTMLButtonElement>('[data-instagram-carousel-previous]')
const instagramCarouselNext = document.querySelector<HTMLButtonElement>('[data-instagram-carousel-next]')
const instagramCarouselClose = document.querySelector<HTMLButtonElement>('[data-instagram-carousel-close]')
const instagramCarouselOverlay = document.querySelector<HTMLElement>('[data-instagram-carousel-overlay]')
const instagramCarouselPermalink = document.querySelector<HTMLAnchorElement>('#instagram-carousel-permalink')

if (
    instagramCarouselDialog
    && instagramCarouselImage
    && instagramCarouselCount
    && instagramCarouselError
    && instagramCarouselPrevious
    && instagramCarouselNext
    && instagramCarouselClose
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
        instagramCarouselImage.src = slide.src
        instagramCarouselImage.alt = slide.alt
        instagramCarouselCount.textContent = `Photo ${currentSlide + 1} of ${slides.length}`
        instagramCarouselError.hidden = true
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
        openButton?.focus()
        openButton = null
    })

    instagramCarouselImage.addEventListener('error', () => {
        openingThumbnailRect = null
        instagramCarouselError.hidden = false
    })

    instagramCarouselImage.addEventListener('load', () => {
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
