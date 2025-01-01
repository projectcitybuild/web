<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import IPBanListTable from './Partials/IPBanListTable.vue'
import { Paginated } from '../../Data/Paginated'
import { IPBan } from '../../Data/IPBan'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import Card from '../../Components/Card.vue'

interface Props {
    bans: Paginated<IPBan>,
}

const props = defineProps<Props>()
</script>

<template>
    <Head title="Manage Bans"/>

    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        <div class="mx-auto max-w-screen-xl">
            <Card class="overflow-hidden">
                <div
                    class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2">
                        <form class="flex items-center">
                            <label for="simple-search" class="sr-only">Search</label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                         fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    id="simple-search"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="UUID / Name..."
                                    required
                                >
                            </div>
                        </form>
                    </div>
                    <div>
                        <Link
                            href="/manage/ip-bans/create"
                            as="button"
                            class="
                                flex flex-row items-center justify-center px-4 py-2 rounded-lg
                                text-sm text-white bg-blue-700
                                hover:bg-primary-800
                                focus:ring-4 focus:ring-blue-300
                                dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800
                            "
                        >
                            <svg class="size-6 mr-2" fill="currentColor" viewbox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                      d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                            </svg>
                            Create Ban
                        </Link>
                    </div>
                </div>

                <InfinitePagination
                    :initial="bans"
                    v-slot="source"
                    class="overflow-x-auto"
                >
                    <IPBanListTable :bans="source.data"/>
                </InfinitePagination>
            </Card>
        </div>
    </section>
</template>
