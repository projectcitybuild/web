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

        <div v-if="reachedEnd" class="text-sm text-gray-400 p-4 text-center">
            Reached the End
        </div>
    </div>
</template>
