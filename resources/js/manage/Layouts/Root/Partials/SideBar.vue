<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import SideBarMenuItem from './SideBarMenuItem.vue'
import usePermissions from '../../../Composables/usePermissions';
import { filterSidebar } from '../../../Navigation/FilterSidebar';
import { sidebarMenu } from '../../../Navigation/Sidebar';
import { computed } from 'vue';

const { can } = usePermissions()

const filteredMenu = computed(() =>
  filterSidebar(sidebarMenu, can)
)
</script>

<template>
    <aside
        class="
            fixed top-0 left-0 z-40
            w-64 h-screen pt-14
            transition-transform -translate-x-full md:translate-x-0
            border-r border-gray-200 dark:border-gray-700
            bg-white dark:bg-gray-800
        "
        id="drawer-navigation"
    >
        <div class="overflow-y-auto py-5 px-3 h-full bg-white dark:bg-gray-800">
            <ul class="space-y-2">
                <li>
                    <Link
                        href="/manage"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <svg
                            class="w-6 h-6 text-gray-800 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"/>
                        </svg>
                        <span class="ml-3">Overview</span>
                    </Link>
                </li>

                <ul>
                    <SideBarMenuItem
                        v-for="section in filteredMenu"
                        :key="section.title"
                        :title="section.title"
                        :icon="section.icon"
                        :children="section.children"
                    />
                </ul>
            </ul>
            <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                <li>
                    <Link
                        href="/manage/activity"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg transition duration-75 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-white group"
                    >
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                             xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.03v13m0-13c-2.819-.831-4.715-1.076-8.029-1.023A.99.99 0 0 0 3 6v11c0 .563.466 1.014 1.03 1.007 3.122-.043 5.018.212 7.97 1.023m0-13c2.819-.831 4.715-1.076 8.029-1.023A.99.99 0 0 1 21 6v11c0 .563-.466 1.014-1.03 1.007-3.122-.043-5.018.212-7.97 1.023"/>
                        </svg>
                        <span class="ml-3">Audit Logs</span>
                    </Link>
                </li>
            </ul>
        </div>
    </aside>
</template>
