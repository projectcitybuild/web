<script setup lang="ts" generic="T">
import { Paginated } from '../Data/Paginated'
import { onBeforeMount, onBeforeUnmount, onMounted, ref, shallowRef, watch } from 'vue'
import axios from 'axios'
import { useIntersectionObserver, watchDebounced } from '@vueuse/core'
import SpinnerRow from './SpinnerRow.vue'
import SvgIcon from './SvgIcon.vue'
import Button from './Button.vue'
import { Icons } from '../Components/SvgIcon.vue'
import { nextTableSort, type TableSortDirection } from '../Composables/useTableSorting'

type QueryValue = string | number | boolean | null | undefined

interface Props {
    initial?: Paginated<T>,
    query?: Record<string, QueryValue>,
    path?: string,
    sort?: string | null,
    direction?: TableSortDirection | null,
}
const props = defineProps<Props>()

const itemCount = defineModel<number>('count', { default: 0 })

const items = shallowRef<T[]>(props.initial?.data ?? [])
const currentPage = ref(props.initial?.current_page ?? 1)
const reachedEnd = ref(props.initial?.next_page_url == null)
const lastElement = ref<HTMLDivElement | null>(null)
const loading = ref(false)
const forbidden = ref(false)
const error = ref<string | null>(null)
const failedPage = ref<number | null>(null)
const sort = ref(props.sort ?? null)
const direction = ref(props.direction ?? null)
let latestRequestId = 0
let activeController: AbortController | null = null

watchDebounced(
    () => props.query,
    () => {
        void loadFirstPage()
    },
    { debounce: 500 },
)

watch(
    () => props.query,
    () => {
        invalidateRequest()
        resetResults()
        loading.value = true
    },
)

function query(page: number): URLSearchParams {
    const params = new URLSearchParams()

    Object.entries(props.query ?? {}).forEach(([key, value]) => {
        if (value !== null && value !== undefined) {
            params.set(key, String(value))
        }
    })

    if (sort.value !== null && direction.value !== null) {
        params.set('sort', sort.value)
        params.set('direction', direction.value)
    }

    if (page > 1) {
        params.set('page', String(page))
    }

    return params
}

function pagePath(page: number): string {
    const base = props.path ?? props.initial?.path
    const params = query(page)

    if (!base) {
        throw new Error('InfinitePagination requires a path when no initial page is provided.')
    }

    if (params.size == 0) {
        return base
    }
    return base + '?' + params.toString()
}

function invalidateRequest(): void {
    latestRequestId += 1
    activeController?.abort()
    activeController = null
    loading.value = false
}

function resetResults(): void {
    currentPage.value = 1
    reachedEnd.value = false
    forbidden.value = false
    error.value = null
    failedPage.value = null
    items.value = []
    itemCount.value = 0
}

const load = async (page: number): Promise<{ requestId: number, data: Paginated<T> } | null> => {
    const controller = new AbortController()
    const requestId = ++latestRequestId
    activeController = controller
    loading.value = true
    error.value = null
    failedPage.value = null

    try {
        const response = await axios.get<Paginated<T>>(pagePath(page), { signal: controller.signal })

        if (requestId !== latestRequestId) {
            return null
        }

        return { requestId, data: response.data }
    } catch (caughtError) {
        if (requestId !== latestRequestId || controller.signal.aborted || axios.isCancel(caughtError)) {
            return null
        }

        if (axios.isAxiosError(caughtError) && caughtError.response?.status === 403) {
            forbidden.value = true
            reachedEnd.value = true
        } else {
            failedPage.value = page
            error.value = 'Unable to load results. Please try again.'
        }
    } finally {
        if (requestId === latestRequestId) {
            activeController = null
            loading.value = false
        }
    }

    return null
}

const loadFirstPage = async (): Promise<void> => {
    invalidateRequest()
    resetResults()

    const result = await load(1)

    if (result === null || result.requestId !== latestRequestId) {
        return
    }

    currentPage.value = 1
    reachedEnd.value = result.data.next_page_url == null
    items.value = result.data.data ?? []
    itemCount.value = items.value.length
}

const loadNextPage = async (): Promise<void> => {
    if (loading.value || reachedEnd.value || forbidden.value) {
        return
    }

    const page = currentPage.value + 1
    const result = await load(page)

    if (result === null || result.requestId !== latestRequestId) {
        return
    }

    currentPage.value = page
    reachedEnd.value = result.data.next_page_url == null
    items.value = [...items.value, ...(result.data.data ?? [])]
    itemCount.value = items.value.length
}

function onSort(key: string): void {
    const next = nextTableSort({ sort: sort.value, direction: direction.value }, key)
    sort.value = next.sort
    direction.value = next.direction
    void loadFirstPage()
}

function retry(): void {
    if (failedPage.value === 1) {
        void loadFirstPage()
    } else if (failedPage.value !== null) {
        void loadNextPage()
    }
}

useIntersectionObserver(
    lastElement,
    ([{isIntersecting}]) => {
        if (isIntersecting) {
            void loadNextPage()
        }
    }
)

onMounted(() => {
    if (props.initial == null) {
        void loadFirstPage()
    }
})

onBeforeMount(() => itemCount.value = items.value.length)
onBeforeUnmount(() => invalidateRequest())
</script>

<template>
    <div class="infinite-pagination">
        <slot :data="items" :loading="loading" :sort="sort" :direction="direction" :on-sort="onSort" :forbidden="forbidden" :error="error" :retry="retry" />

        <!-- Invisible element to trigger loading next page -->
        <div ref="lastElement" class="-translate-y-32"></div>

        <div v-if="error && !forbidden" class="flex items-center justify-between gap-3 border-x border-b border-danger bg-danger-soft p-3 text-xs text-danger" role="alert">
            <span>{{ error }}</span>
            <Button variant="outline" size="sm" @click="retry">Retry</Button>
        </div>

        <div v-else-if="reachedEnd && !forbidden" class="pagination-end flex items-center justify-center gap-2 rounded-none border-x border-b border-border-table bg-surface-muted p-3 text-xs text-muted">
            <SvgIcon :svg="Icons.hand" class="size-4" aria-hidden="true" />
            Reached the End
        </div>

        <SpinnerRow v-else-if="loading && !forbidden" class="pagination-spinner rounded-none border-x border-b border-border-table" />
    </div>
</template>

<style scoped>
.infinite-pagination :deep(.table-frame) {
    border-bottom: 0;
}
</style>
