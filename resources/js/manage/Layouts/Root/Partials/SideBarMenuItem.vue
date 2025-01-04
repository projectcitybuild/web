<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { onBeforeMount, ref, useId, watch } from 'vue'

interface MenuItem {
    title: string,
    route: string,
}

interface Props {
    title: string,
    children: MenuItem[],
}

const id = useId()
const props = defineProps<Props>()
const selectedParent = defineModel('selected')
const selectedRoute = ref<MenuItem>(null)
const page = usePage()

function select() {
    if (selectedParent.value == id) {
        selectedParent.value = null
        checkSelection()
    } else {
        selectedParent.value = id
    }
}

function checkSelection() {
    const selectedChild = props.children.find(
        (it) => page.url.startsWith(it.route)
    )
    if (selectedChild) {
        selectedParent.value = id
        selectedRoute.value = selectedChild.route
    }
}

onBeforeMount(checkSelection)

watch(() => page.url, checkSelection)
</script>

<template>
    <li>
        <button
            type="button"
            class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
            @click="select"
        >
            <slot name="icon"></slot>
            <span class="flex-1 ml-3 text-left whitespace-nowrap">{{ props.title }}</span>

            <svg v-if="selectedParent == id" class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 15 7-7 7 7"/>
            </svg>
            <svg v-else class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
            </svg>
        </button>

        <ul class="py-2 space-y-2" v-if="selectedParent == id">
            <li v-for="item in props.children">
                <Link
                    :href="item.route"
                    :class="
                        (selectedRoute == item.route ? 'font-bold ' : '') +
                        'flex items-center p-2 pl-11 w-full text-sm text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700'
                    "
                >
                    {{ item.title }}
                </Link>
            </li>
        </ul>
    </li>
</template>
