<script setup lang="ts" generic="T">
import { Paginated } from '../Data/Paginated'
import { onMounted, ref } from 'vue'
import axios, { AxiosResponse } from 'axios'
import { useIntersectionObserver } from '@vueuse/core'

type PageResponse = AxiosResponse<Paginated<T>>

interface Props {
    initial?: Paginated<T>,
    path?: string,
}

const props = defineProps<Props>()

const items = ref<T[]>(props.initial?.data ?? [])
const nextCursor = ref(props.initial?.next_cursor)
const reachedEnd = ref(props.initial?.next_cursor == null ?? false)
const lastElement = ref(null)

const path = props.path ?? props.initial?.path

const loadNextPage = async () => {
    let url = path
    if (nextCursor.value != null) {
        url += `?cursor=${nextCursor.value}`
    }

    const response: PageResponse = await axios.get(url)
    items.value = [
        ...items.value,
        ...response.data.data
    ]
    nextCursor.value = response.data.next_cursor

    if (!response.data.next_cursor) {
        reachedEnd.value = true
        stop()
    }
}

const {stop} = useIntersectionObserver(
    lastElement,
    ([{isIntersecting}]) => {
        if (isIntersecting) loadNextPage()
    }
)

onMounted(() => {
    if (props.initial == null) {
        loadNextPage()
    }
})
</script>

<template>
    <div>
        <slot :data="items"/>

        <!-- Invisible element to trigger loading next page -->
        <div ref="lastElement" class="-translate-y-32"></div>

        <div v-if="reachedEnd" class="text-sm text-gray-400 p-4 text-center">
            Reached the End
        </div>
    </div>
</template>
