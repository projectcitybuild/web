const stickyNavbars = document.querySelectorAll<HTMLElement>('[data-sticky-navbar]')

stickyNavbars.forEach(navbar => {
    const placeholder = document.createElement('div')
    placeholder.setAttribute('aria-hidden', 'true')
    placeholder.style.height = '0px'
    navbar.before(placeholder)

    const navbarTop = placeholder.getBoundingClientRect().top + window.scrollY
    let isPinned = false
    let placeholderHeight = 0

    const updateNavbar = () => {
        const shouldPin = window.scrollY > navbarTop

        if (shouldPin !== isPinned) {
            if (shouldPin) {
                placeholderHeight = navbar.offsetHeight
                placeholder.style.height = `${placeholderHeight}px`
            }

            isPinned = shouldPin
            navbar.classList.toggle('is-pinned', isPinned)

            if (!isPinned) {
                placeholderHeight = 0
                placeholder.style.height = '0px'
            }
        }

        if (isPinned && navbar.offsetHeight !== placeholderHeight) {
            placeholderHeight = navbar.offsetHeight
            placeholder.style.height = `${placeholderHeight}px`
        } else if (!isPinned && placeholderHeight !== 0) {
            placeholderHeight = 0
            placeholder.style.height = '0px'
        }

        if (navbar.dataset.variant === 'clear') {
            const backgroundOpacity = Math.min(window.scrollY / 120, 0.95)

            navbar.style.setProperty('--navbar-background-opacity', backgroundOpacity.toString())
            navbar.style.setProperty('--navbar-border-opacity', (0.65 * (1 - backgroundOpacity / 0.95)).toString())
        }
    }

    window.addEventListener('scroll', updateNavbar, { passive: true })
    window.addEventListener('resize', updateNavbar)
    updateNavbar()
})
