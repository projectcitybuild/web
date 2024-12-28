<script setup lang="ts" generic="T">
import { Paginated } from '../Data/Paginated'
import { ref } from 'vue'
import axios, { AxiosResponse } from 'axios'
import { useIntersectionObserver } from '@vueuse/core'

type PageResponse = AxiosResponse<Paginated<T>>

interface Props {
    paginated: Paginated<T>,
}
const props = defineProps<Props>()

const lastElement = ref(null)
const reachedEnd = ref(false)


const loadNextPage = async () => {
    const url = `${props.paginated.path}?cursor=${props.paginated.next_cursor}`
    const response: PageResponse = await axios.get(url)
    props.paginated.data = [
        ...props.paginated.data,
        ...response.data.data
    ]
    if (!response.next_cursor) {
        reachedEnd.value = true
        stop()
    }
}

const { stop } = useIntersectionObserver(
    lastElement,
    ([{ isIntersecting }]) => {
        if (isIntersecting) loadNextPage()
    }
)
</script>

<template>
    <div>
        <slot :data="paginated.data" />

        <!-- Invisible element to trigger loading next page -->
        <div ref="lastElement" class="-translate-y-32"></div>

        <div v-if="reachedEnd" class="text-sm text-gray-400 p-4 text-center">
            Reached the End
        </div>
    </div>
</template>
