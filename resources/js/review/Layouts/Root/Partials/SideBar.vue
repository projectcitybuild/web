<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import Badge from '../../../../manage/Components/Badge.vue'
import usePermissions from '../../../../manage/Composables/usePermissions'

const page = usePage()
const { can } = usePermissions()

const pendingAppealCount = computed(() => page.props.pending_appeals ?? 0)
const pendingBuildRankAppCount = computed(() => page.props.pending_build_rank_apps ?? 0)
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
                        href="/review"
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
                <li v-if="can('web.review.ban_appeals.view')">
                    <Link
                        href="/review/ban-appeals"
                        class="flex justify-between items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <svg
                            class="w-6 h-6 text-gray-800 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.5 21h13M12 21V7m0 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm2-1.8c3.073.661 2.467 2.8 5 2.8M5 8c3.359 0 2.192-2.115 5.012-2.793M7 9.556V7.75m0 1.806-1.95 4.393a.773.773 0 0 0 .37.962.785.785 0 0 0 .362.089h2.436a.785.785 0 0 0 .643-.335.776.776 0 0 0 .09-.716L7 9.556Zm10 0V7.313m0 2.243-1.95 4.393a.773.773 0 0 0 .37.962.786.786 0 0 0 .362.089h2.436a.785.785 0 0 0 .643-.335.775.775 0 0 0 .09-.716L17 9.556Z"/>
                        </svg>

                        <div class="ml-3 grow">
                            Ban Appeals
                        </div>

                        <Badge
                            v-if="pendingAppealCount > 0"
                            :count="pendingAppealCount"
                        />
                    </Link>
                </li>
                <li v-if="can('web.review.build_rank_apps.view')">
                    <Link
                        href="/review/builder-ranks"
                        class="flex justify-between items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <svg
                            class="w-6 h-6 text-gray-800 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z"/>
                        </svg>

                        <span class="ml-3 grow">
                            Builder Applications
                        </span>

                        <Badge
                            v-if="pendingBuildRankAppCount > 0"
                            :count="pendingBuildRankAppCount"
                        />
                    </Link>
                </li>
            </ul>
        </div>
    </aside>
</template>
