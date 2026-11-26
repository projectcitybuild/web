<script setup lang="ts">
import type { components } from '../generated/openapi'
import { computed, onBeforeUnmount, ref, watch } from 'vue'

type Ban = Pick<components['schemas']['GamePlayerBan'], 'id' | 'reason' | 'created_at'> & {
    name: string,
    uuid: string | null,
    avatar_url: string | null,
    appeal_url: string,
    details_url: string,
}

interface BanListResults {
    current_page: number,
    data: Ban[],
    from: number | null,
    last_page: number,
    per_page: number,
    to: number | null,
    total: number,
}

type SortKey = 'name' | 'reason' | 'created_at'
type SortDirection = 'asc' | 'desc'

const props = defineProps<{
    endpoint: string,
    initialQuery: string,
    initialResults: BanListResults,
}>()

const searchTerm = ref(props.initialQuery)
const results = ref(props.initialResults)
const sortKey = ref<SortKey | null>(null)
const sortDirection = ref<SortDirection | null>(null)
const isSearching = ref(false)
const searchError = ref<string | null>(null)

let searchTimer: number | undefined
let activeRequest: AbortController | null = null

const sortedBans = computed(() => {
    const key = sortKey.value
    const direction = sortDirection.value

    if (!key || !direction) {
        return results.value.data
    }

    return [...results.value.data].sort((left, right) => {
        const comparison = key === 'created_at'
            ? Date.parse(left.created_at ?? '') - Date.parse(right.created_at ?? '')
            : (left[key] ?? '').localeCompare(right[key] ?? '', undefined, {
                numeric: true,
                sensitivity: 'base',
            })

        return direction === 'asc' ? comparison : -comparison
    })
})

watch(searchTerm, (query) => {
    window.clearTimeout(searchTimer)
    searchTimer = window.setTimeout(() => {
        void loadBans(query, 1)
    }, 250)
})

onBeforeUnmount(() => {
    window.clearTimeout(searchTimer)
    activeRequest?.abort()
})

function toggleSort(key: SortKey): void {
    if (sortKey.value !== key) {
        sortKey.value = key
        sortDirection.value = 'asc'
        return
    }

    if (sortDirection.value === 'asc') {
        sortDirection.value = 'desc'
        return
    }

    sortKey.value = null
    sortDirection.value = null
}

function sortState(key: SortKey): 'ascending' | 'descending' | 'none' {
    if (sortKey.value !== key || sortDirection.value === null) {
        return 'none'
    }

    return sortDirection.value === 'asc' ? 'ascending' : 'descending'
}

function formatBanDate(value: string | null): string {
    if (value === null) {
        return 'Unknown date'
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return 'Unknown date'
    }

    return new Intl.DateTimeFormat(undefined, {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date)
}

function submitSearch(): void {
    window.clearTimeout(searchTimer)
    void loadBans(searchTerm.value, 1)
}

async function loadBans(query: string, page: number): Promise<void> {
    activeRequest?.abort()
    const request = new AbortController()
    activeRequest = request
    isSearching.value = true
    searchError.value = null

    const normalizedQuery = query.trim()
    const url = new URL(props.endpoint, window.location.origin)

    if (normalizedQuery) {
        url.searchParams.set('query', normalizedQuery)
    }

    if (page > 1) {
        url.searchParams.set('page', String(page))
    }

    try {
        const response = await fetch(url, {
            headers: { Accept: 'application/json' },
            signal: request.signal,
        })

        if (!response.ok) {
            throw new Error(`Ban search failed with status ${response.status}.`)
        }

        results.value = await response.json() as BanListResults
        window.history.replaceState(window.history.state, '', `${url.pathname}${url.search}`)
    } catch (error) {
        if (error instanceof DOMException && error.name === 'AbortError') {
            return
        }

        console.error('Unable to search active bans.', error)
        searchError.value = 'Search could not be completed. Please try again.'
    } finally {
        if (activeRequest === request) {
            activeRequest = null
            isSearching.value = false
        }
    }
}
</script>

<template>
    <div class="ban-list space-y-5">
        <form class="form-search" role="search" @submit.prevent="submitSearch">
            <div class="form-search__field">
                <label for="ban-query" class="form-field__label">
                    Search by player name or UUID
                </label>
                <input
                    id="ban-query"
                    v-model="searchTerm"
                    class="form-control"
                    name="query"
                    type="search"
                    placeholder="Search bans..."
                    autocomplete="off"
                >
            </div>
            <button class="form-button form-button--secondary" type="submit">
                Search
            </button>
        </form>

        <p v-if="isSearching" class="text-sm text-gray-400" role="status" aria-live="polite">
            Searching...
        </p>
        <p v-if="searchError" class="rounded-lg border border-red-300/30 bg-red-950/45 p-3 text-sm text-red-200" role="alert">
            {{ searchError }}
        </p>

        <div v-if="results.total === 0" class="form-empty rounded-xl border border-white/10 bg-white/5">
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-10 text-form-accent">
                <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 0 0-1.071-.136 9.742 9.742 0 0 0-3.539 6.176 7.547 7.547 0 0 1-1.705-1.715.75.75 0 0 0-1.152-.082A9 9 0 1 0 15.68 4.534a7.46 7.46 0 0 1-2.717-2.248ZM15.75 14.25a3.75 3.75 0 1 1-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 0 1 1.925-3.546 3.75 3.75 0 0 1 3.255 3.718Z" clip-rule="evenodd" />
            </svg>
            <h2 class="text-xl font-bold text-white">No results</h2>
            <p>Try a different search or check back later.</p>
        </div>

        <template v-else>
            <div class="data-table-wrap">
                <table class="data-table">
                    <thead class="data-table__head">
                        <tr>
                            <th class="data-table__cell cursor-pointer transition-colors hover:bg-white/5 hover:text-white" scope="col" :aria-sort="sortState('name')" @click="toggleSort('name')">
                                <button class="inline-flex items-center gap-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-form-accent" type="button" @click.stop="toggleSort('name')">
                                    Player Name
                                    <svg
                                        v-if="sortKey === 'name'"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        class="size-4"
                                        :class="{ 'rotate-180': sortDirection === 'desc' }"
                                    >
                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </th>
                            <th class="data-table__cell cursor-pointer transition-colors hover:bg-white/5 hover:text-white" scope="col" :aria-sort="sortState('reason')" @click="toggleSort('reason')">
                                <button class="inline-flex items-center gap-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-form-accent" type="button" @click.stop="toggleSort('reason')">
                                    Reason
                                    <svg
                                        v-if="sortKey === 'reason'"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        class="size-4"
                                        :class="{ 'rotate-180': sortDirection === 'desc' }"
                                    >
                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </th>
                            <th class="data-table__cell cursor-pointer transition-colors hover:bg-white/5 hover:text-white" scope="col" :aria-sort="sortState('created_at')" @click="toggleSort('created_at')">
                                <button class="inline-flex items-center gap-1 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-form-accent" type="button" @click.stop="toggleSort('created_at')">
                                    Ban Date
                                    <svg
                                        v-if="sortKey === 'created_at'"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        class="size-4"
                                        :class="{ 'rotate-180': sortDirection === 'desc' }"
                                    >
                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </th>
                            <th class="data-table__cell" scope="col">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ban in sortedBans" :key="ban.id" class="data-table__row">
                            <td class="data-table__cell">
                                <span class="flex items-center gap-3 whitespace-nowrap font-semibold text-white">
                                    <img
                                        v-if="ban.avatar_url"
                                        :src="ban.avatar_url"
                                        alt=""
                                        class="size-6 shrink-0 rounded-md"
                                    >
                                    {{ ban.name }}
                                </span>
                            </td>
                            <td class="data-table__cell text-gray-300">
                                <span class="block max-w-sm truncate" :title="ban.reason ?? '-'">
                                    {{ ban.reason ?? '-' }}
                                </span>
                            </td>
                            <td class="data-table__cell whitespace-nowrap text-gray-300">
                                <time v-if="ban.created_at" :datetime="ban.created_at">
                                    {{ formatBanDate(ban.created_at) }}
                                </time>
                                <span v-else>Unknown date</span>
                            </td>
                            <td class="data-table__cell">
                                <div class="data-table__actions">
                                    <a class="form-button form-button--secondary whitespace-nowrap px-3 py-2 text-sm" :href="ban.appeal_url">
                                        Appeal
                                    </a>
                                    <a class="form-button whitespace-nowrap px-3 py-2 text-sm" :href="ban.details_url">
                                        Details
                                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                            <path fill-rule="evenodd" d="M6.22 4.22a.75.75 0 0 1 1.06 0l3.25 3.25a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06-1.06L8.94 8 6.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-white/10 pt-5">
                <p class="text-sm text-gray-400">
                    Showing {{ results.from }}-{{ results.to }} of {{ results.total }} bans
                </p>
                <nav v-if="results.last_page > 1" class="flex items-center gap-2" aria-label="Ban list pages">
                    <button
                        class="form-button form-button--secondary px-3 py-2 text-sm"
                        type="button"
                        :disabled="isSearching || results.current_page <= 1"
                        @click="loadBans(searchTerm, results.current_page - 1)"
                    >
                        Previous
                    </button>
                    <span class="whitespace-nowrap text-sm text-gray-400">
                        Page {{ results.current_page }} of {{ results.last_page }}
                    </span>
                    <button
                        class="form-button form-button--secondary px-3 py-2 text-sm"
                        type="button"
                        :disabled="isSearching || results.current_page >= results.last_page"
                        @click="loadBans(searchTerm, results.current_page + 1)"
                    >
                        Next
                    </button>
                </nav>
            </div>
        </template>
    </div>
</template>
