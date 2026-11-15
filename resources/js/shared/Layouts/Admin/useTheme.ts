import { computed, ref } from 'vue'

type Theme = 'light' | 'dark'

const theme = ref<Theme>('light')
let initialized = false

function applyTheme(value: Theme): void {
    theme.value = value
    document.documentElement.classList.toggle('dark', value === 'dark')
}

function initializeTheme(): void {
    if (initialized || typeof window === 'undefined') {
        return
    }

    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
    const storedTheme = window.localStorage.getItem('admin-theme')
    applyTheme(storedTheme === 'light' || storedTheme === 'dark'
        ? storedTheme
        : mediaQuery.matches ? 'dark' : 'light')

    mediaQuery.addEventListener('change', event => {
        if (!window.localStorage.getItem('admin-theme')) {
            applyTheme(event.matches ? 'dark' : 'light')
        }
    })

    initialized = true
}

export default function useTheme() {
    initializeTheme()

    function setTheme(value: Theme): void {
        window.localStorage.setItem('admin-theme', value)
        applyTheme(value)
    }

    function toggleTheme(): void {
        setTheme(theme.value === 'dark' ? 'light' : 'dark')
    }

    return {
        theme,
        isDark: computed(() => theme.value === 'dark'),
        setTheme,
        toggleTheme,
    }
}
