const donationBackground = document.querySelector<HTMLElement>('[data-donation-background]')

if (donationBackground) {
    const parallaxLayer = donationBackground.querySelector<HTMLElement>('[data-donation-parallax]')
    const donationHero = document.querySelector<HTMLElement>('[data-donation-hero]')

    if (!parallaxLayer || !donationHero) {
        throw new Error('The donation background is missing required page elements.')
    }

    const updateBackground = () => {
        const fadeDistance = Math.max(donationHero.offsetHeight, 1)
        const opacity = Math.max(0, 1 - window.scrollY / fadeDistance)
        const parallaxOffset = -Math.min(window.scrollY * 0.04, window.innerHeight * 0.07)

        donationBackground.style.opacity = opacity.toString()
        parallaxLayer.style.transform = `translate3d(0, ${parallaxOffset}px, 0)`
    }

    window.addEventListener('scroll', updateBackground, { passive: true })
    window.addEventListener('resize', updateBackground)
    updateBackground()
}
