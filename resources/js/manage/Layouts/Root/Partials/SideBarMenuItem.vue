<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { ref, useId } from 'vue'

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
const selected = defineModel('selected')

function select() {
    if (selected.value == id) {
        selected.value = null
    } else {
        selected.value = id
    }
}
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

            <svg v-if="selected == id" class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 15 7-7 7 7"/>
            </svg>
            <svg v-else class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
            </svg>
        </button>
        <ul class="py-2 space-y-2" v-show="selected == id">
            <li v-for="item in props.children">
                <Link
                    :href="item.route"
                    class="flex items-center p-2 pl-11 w-full text-sm text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                >
                    {{ item.title }}
                </Link>
            </li>
        </ul>
    </li>
</template>
