import { computed, ref } from 'vue'

type Theme = 'light' | 'dark'
type ThemePreference = Theme | 'system'

const preference = ref<ThemePreference>('system')
const prefersDark = ref(false)
const theme = computed<Theme>(() => preference.value === 'system'
    ? prefersDark.value ? 'dark' : 'light'
    : preference.value)
let initialized = false

function applyTheme(): void {
    document.documentElement.classList.toggle('dark', theme.value === 'dark')
}

function initializeTheme(): void {
    if (initialized || typeof window === 'undefined') {
        return
    }

    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
    const storedTheme = window.localStorage.getItem('admin-theme')
    prefersDark.value = mediaQuery.matches
    preference.value = storedTheme === 'light' || storedTheme === 'dark' || storedTheme === 'system'
        ? storedTheme
        : 'system'
    applyTheme()

    mediaQuery.addEventListener('change', event => {
        prefersDark.value = event.matches
        if (preference.value === 'system') {
            applyTheme()
        }
    })

    window.addEventListener('storage', event => {
        if (event.key !== 'admin-theme' && event.key !== null) {
            return
        }

        const storedTheme = window.localStorage.getItem('admin-theme')
        preference.value = storedTheme === 'light' || storedTheme === 'dark' || storedTheme === 'system'
            ? storedTheme
            : 'system'
        applyTheme()
    })

    initialized = true
}

export default function useTheme() {
    initializeTheme()

    function setTheme(value: ThemePreference): void {
        window.localStorage.setItem('admin-theme', value)
        preference.value = value
        applyTheme()
    }

    function cycleTheme(): void {
        const nextTheme: Record<ThemePreference, ThemePreference> = {
            light: 'dark',
            dark: 'system',
            system: 'light',
        }
        setTheme(nextTheme[preference.value])
    }

    return {
        theme,
        preference,
        isDark: computed(() => theme.value === 'dark'),
        setTheme,
        cycleTheme,
    }
}
