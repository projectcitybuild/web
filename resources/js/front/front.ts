import { createApp } from 'vue'
import InstagramCarousel from './InstagramCarousel.vue'

// Inform Vite where static assets are located
import.meta.glob([
    '../../images/**',
]);

import './player-report-form'

import './menu-toggles'
import './sticky-navbar'
import './home-reveal'
import './server-address-copy'

const instagramCarouselElement = document.querySelector<HTMLElement>('#instagram-carousel-app')

if (instagramCarouselElement) {
    createApp(InstagramCarousel).mount(instagramCarouselElement)
}
