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
