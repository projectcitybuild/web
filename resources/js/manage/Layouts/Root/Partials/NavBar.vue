<script setup lang="ts">
import logo from '../../../../../images/logo-alt.png'
import { onBeforeMount, onMounted, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Drawer } from 'flowbite'

const page = usePage()

const selectedTab = ref(checkTab(page.url))
const drawer = ref<Drawer|null>(null)

function checkTab(url: string) {
    if (url.startsWith('/manage')) {
        return 'manage'
    } else if (url.startsWith('/review')) {
        return 'review'
    }
    return null
}

onBeforeMount(() => checkTab(page.url))

watch(() => page.url, checkTab)

onMounted(() => {
    const $targetEl = document.getElementById('drawer-navigation')
    const options = {
        placement: 'left',
        backdrop: true,
        bodyScrolling: false,
        edge: false,
        edgeOffset: '',
        backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-30',
    }
    const instanceOptions = {
        id: 'drawer',
        override: true
    }
    drawer.value = new Drawer($targetEl, options, instanceOptions)
})
</script>

<template>
    <nav class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
        <div class="flex flex-wrap justify-between items-center">
            <a href="/" class="flex items-center justify-between mr-4">
                <img :src="logo" alt="Project City Build" />
            </a>

            <div class="flex items-center lg:order-2">
                <div class="p-2 text-xs md:text-sm">
                    <a href="/manage" :class="'p-3' + (selectedTab === 'manage' ? ' rounded-lg bg-gray-100 font-bold' : '')">Manage</a>
                    <a href="/review" :class="'p-3' + (selectedTab === 'review' ? ' rounded-lg bg-gray-100 font-bold' : '')">Review</a>
                </div>
            </div>

            <button
                data-drawer-target="drawer-navigation"
                data-drawer-toggle="drawer-navigation"
                aria-controls="drawer-navigation"
                class="p-2 mr-2 text-gray-600 rounded-lg cursor-pointer md:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                @click="drawer?.toggle()"
            >
                <svg
                    aria-hidden="true"
                    class="w-6 h-6"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        fill-rule="evenodd"
                        d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                        clip-rule="evenodd"
                    ></path>
                </svg>
                <svg
                    aria-hidden="true"
                    class="hidden w-6 h-6"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    ></path>
                </svg>
                <span class="sr-only">Toggle sidebar</span>
            </button>
        </div>
    </nav>
</template>
