<script setup lang="ts" generic="T">
import { Paginated } from '../Data/Paginated'
import { onMounted, ref, watch } from 'vue'
import axios, { AxiosResponse } from 'axios'
import { useIntersectionObserver, watchDebounced } from '@vueuse/core'

type PageResponse = AxiosResponse<Paginated<T>>

interface Props {
    initial?: Paginated<T>,
    query?: object,
    path?: string,
}

const props = defineProps<Props>()

const items = ref<T[]>(props.initial?.data ?? [])
const nextCursor = ref(props.initial?.next_cursor)
const reachedEnd = ref(props.initial?.next_cursor == null ?? false)
const lastElement = ref(null)
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

function query() {
    if (nextCursor.value != null) {
        return new URLSearchParams({
            ...props.query,
            cursor: nextCursor.value,
        })
    }
    return new URLSearchParams(props.query)
}

function pagePath() {
    const base = props.path ?? props.initial?.path
    const params = query()

    if (params.size == 0) {
        return base
    }
    return base + '?' + params.toString()
}

const load = async () => {
    try {
        const path = pagePath()
        const response: PageResponse = await axios.get(path)
        const cursor = response.data.next_cursor

        nextCursor.value = cursor
        reachedEnd.value = cursor == null
        if (cursor == null) {
            stop()
        }
        return response.data.data
    } finally {
        loading.value = false
    }
}

const loadFirstPage = async () => {
    nextCursor.value = null
    reachedEnd.value = false

    items.value = await load()
}

const loadNextPage = async () => {
    items.value = [
        ...items.value,
        ...await load(),
    ]
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
</script>

<template>
    <div>
        <slot :data="items" :loading="loading" />

        <!-- Invisible element to trigger loading next page -->
        <div ref="lastElement" class="-translate-y-32"></div>

        <div v-if="reachedEnd" class="text-sm text-gray-400 p-4 bg-gray-50 flex gap-2 justify-center items-center border-t border-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
            </svg>
            Reached the End
        </div>
    </div>
</template>
