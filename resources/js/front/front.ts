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
