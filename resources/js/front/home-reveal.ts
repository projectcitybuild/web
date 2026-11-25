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
