import createClient from 'openapi-fetch'
import type { paths } from '../generated/openapi'

const apiBaseUrl = document.querySelector<HTMLMetaElement>('meta[name="api-base-url"]')?.content

if (!apiBaseUrl) {
    throw new Error('The API base URL is missing from the page.')
}

export const api = createClient<paths>({
    baseUrl: apiBaseUrl,
})
