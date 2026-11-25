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
