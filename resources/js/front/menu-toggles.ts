const menuToggles = document.querySelectorAll<HTMLButtonElement>('[data-menu-toggle-target]')

const getMenuTarget = (toggle: HTMLButtonElement): HTMLElement => {
    const targetId = toggle.dataset.menuToggleTarget

    if (!targetId) {
        throw new Error('Menu toggle is missing its target ID.')
    }

    const target = document.getElementById(targetId)

    if (!(target instanceof HTMLElement)) {
        throw new Error(`Menu toggle target "${targetId}" was not found.`)
    }

    return target
}

const setMenuOpen = (toggle: HTMLButtonElement, isOpen: boolean): void => {
    getMenuTarget(toggle).classList.toggle('hidden', !isOpen)
    toggle.setAttribute('aria-expanded', isOpen.toString())
}

menuToggles.forEach(toggle => {
    toggle.addEventListener('click', () => {
        setMenuOpen(toggle, toggle.getAttribute('aria-expanded') !== 'true')
    })
})

document.addEventListener('click', event => {
    const clickedElement = event.target

    if (!(clickedElement instanceof Node)) {
        return
    }

    menuToggles.forEach(toggle => {
        if (toggle.dataset.menuDismiss !== 'outside' || toggle.getAttribute('aria-expanded') !== 'true') {
            return
        }

        const target = getMenuTarget(toggle)

        if (!toggle.contains(clickedElement) && !target.contains(clickedElement)) {
            setMenuOpen(toggle, false)
        }
    })
})

document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') {
        return
    }

    menuToggles.forEach(toggle => {
        if (toggle.getAttribute('aria-expanded') === 'true') {
            setMenuOpen(toggle, false)
        }
    })
})
