import { createApp } from 'vue'
import BanList from './BanList.vue'

interface BanListResults {
    current_page: number,
    data: unknown[],
    from: number | null,
    last_page: number,
    per_page: number,
    to: number | null,
    total: number,
}

const element = document.querySelector<HTMLElement>('#ban-list')

if (element) {
    const endpoint = element.dataset.endpoint
    const initialResults = element.dataset.initialResults

    if (!endpoint || !initialResults) {
        throw new Error('The ban list is missing its initial data.')
    }

    createApp(BanList, {
        endpoint,
        initialQuery: element.dataset.initialQuery ?? '',
        initialResults: JSON.parse(initialResults) as BanListResults,
    }).mount(element)
}
