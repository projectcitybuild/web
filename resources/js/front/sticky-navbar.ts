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
