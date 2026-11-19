<script setup lang="ts" generic="T">
import { Paginated } from '../Data/Paginated'
import { onBeforeMount, onMounted, ref, shallowRef, watch } from 'vue'
import axios from 'axios'
import { useIntersectionObserver, watchDebounced } from '@vueuse/core'
import SpinnerRow from './SpinnerRow.vue'
import SvgIcon from './SvgIcon.vue'
import { Icons } from '../Navigation/Icons'

type QueryValue = string | number | boolean | null | undefined

interface Props {
    initial?: Paginated<T>,
    query?: Record<string, QueryValue>,
    path?: string,
}
const props = defineProps<Props>()

const itemCount = defineModel<number>('count', { default: 0 })

const items = shallowRef<T[]>(props.initial?.data ?? [])
const currentPage = ref(props.initial?.current_page ?? 1)
const reachedEnd = ref(props.initial?.next_page_url == null)
const lastElement = ref<HTMLDivElement | null>(null)
const loading = ref(false)

watchDebounced(
    () => props.query,
    () => loadFirstPage(),
    { debounce: 500 },
)

watch(
    () => props.query,
    () => loading.value = true,
)

function query(): URLSearchParams {
    const params = new URLSearchParams()

    Object.entries(props.query ?? {}).forEach(([key, value]) => {
        if (value !== null && value !== undefined) {
            params.set(key, String(value))
        }
    })

    if (currentPage.value > 1) {
        params.set('page', String(currentPage.value))
    }

    return params
}

function pagePath(): string {
    const base = props.path ?? props.initial?.path
    const params = query()

    if (!base) {
        throw new Error('InfinitePagination requires a path when no initial page is provided.')
    }

    if (params.size == 0) {
        return base
    }
    return base + '?' + params.toString()
}

const load = async () => {
    try {
        const path = pagePath()
        const response = await axios.get<Paginated<T>>(path)

        reachedEnd.value = response.data.next_page_url == null
        if (reachedEnd.value) {
            stop()
        }
        return response.data?.data ?? []
    } finally {
        loading.value = false
    }
}

const loadFirstPage = async () => {
    currentPage.value = 1
    reachedEnd.value = false

    items.value = await load()
    itemCount.value = items.value.length
}

const loadNextPage = async () => {
    currentPage.value += 1

    items.value = [
        ...items.value,
        ...await load(),
    ]
    itemCount.value = items.value.length
}

const { stop } = useIntersectionObserver(
    lastElement,
    ([{isIntersecting}]) => {
        if (isIntersecting) loadNextPage()
    }
)

onMounted(() => {
    if (props.initial == null) {
        loadFirstPage()
    }
})

onBeforeMount(() => itemCount.value = items.value.length)
</script>

<template>
    <div class="infinite-pagination">
        <slot :data="items" :loading="loading" />

        <!-- Invisible element to trigger loading next page -->
        <div ref="lastElement" class="-translate-y-32"></div>

        <div v-if="reachedEnd" class="pagination-end flex items-center justify-center gap-2 rounded-none border-x border-b border-border-table bg-surface-muted p-3 text-xs text-muted">
            <SvgIcon :svg="Icons.hand" class="size-4" aria-hidden="true" />
            Reached the End
        </div>

        <SpinnerRow v-else class="pagination-spinner rounded-none border-x border-b border-border-table" />
    </div>
</template>

<style scoped>
.infinite-pagination :deep(.table-frame) {
    border-bottom: 0;
}
</style>
