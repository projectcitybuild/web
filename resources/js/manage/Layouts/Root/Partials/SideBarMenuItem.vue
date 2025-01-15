<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { onBeforeMount, ref, useId, watch, computed } from 'vue'
import SvgIcon from '../../../Components/SvgIcon.vue'

interface MenuItem {
    title: string,
    route: string,
}

interface Props {
    title: string,
    children: MenuItem[],
}

const id = useId()
const page = usePage()


const props = defineProps<Props>()
const expanded = ref(false)
const selectedChild = ref<MenuItem|null>(getSelectedChild(page.url))

function getSelectedChild(url: string) {
    return props.children.find(
        (child) => url.startsWith(child.route)
    )
}

function checkSelection(url: string) {
    selectedChild.value = getSelectedChild(url)
    expanded.value = selectedChild.value != null
}

onBeforeMount(() => checkSelection(page.url))

watch(() => page.url, checkSelection)
</script>

<template>
    <li>
        <button
            type="button"
            class="flex items-center p-2 w-full text-base text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
            @click="expanded = !expanded"
        >
            <slot name="icon"></slot>
            <span class="flex-1 ml-3 text-left whitespace-nowrap">{{ props.title }}</span>

            <SvgIcon v-if="expanded" icon="chevron-up" :thickness="2" class="size-6" />
            <SvgIcon v-else icon="chevron-down" :thickness="2" class="size-6" />
        </button>

        <ul class="py-2 space-y-2" v-show="expanded">
            <li v-for="item in props.children">
                <Link
                    :href="item.route"
                    :class="
                        (selectedChild?.route === item.route ? 'font-bold ' : '') +
                        'flex items-center p-2 pl-11 w-full text-sm text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700'
                    "
                >
                    {{ item.title }}
                </Link>
            </li>
        </ul>
    </li>
</template>
