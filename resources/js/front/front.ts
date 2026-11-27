import { createApp } from 'vue'
import DonationDialog from './DonationDialog.vue'
import InstagramCarousel from './InstagramCarousel.vue'

// Inform Vite where static assets are located
import.meta.glob([
    '../../images/**',
]);

import './player-report-form'

import './menu-toggles'
import './sticky-navbar'
import './donation-background'
import './home-reveal'
import './server-address-copy'

const instagramCarouselElement = document.querySelector<HTMLElement>('#instagram-carousel-app')

if (instagramCarouselElement) {
    createApp(InstagramCarousel).mount(instagramCarouselElement)
}

const donationDialogElement = document.querySelector<HTMLElement>('#donation-dialog-app')

if (donationDialogElement) {
    const { authenticated, checkoutUrl, csrfToken, loginUrl } = donationDialogElement.dataset

    if (authenticated === undefined || !checkoutUrl || !csrfToken || !loginUrl) {
        throw new Error('The donation dialog is missing required configuration.')
    }

    createApp(DonationDialog, {
        authenticated: authenticated === 'true',
        checkoutUrl,
        csrfToken,
        loginUrl,
    }).mount(donationDialogElement)
}
