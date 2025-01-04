<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import AccountListTable from './Partials/AccountListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import type { Account } from '../../Data/Account'
import { watch, ref } from 'vue'

interface Props {
    success?: string,
    accounts: Paginated<Account>,
}
defineProps<Props>()

const filterExpanded = ref(false)
const query = ref({})

const form = useForm({
    username: null,
    email: null,
    activated: null,
})

watch(
    () => form,
    (form) => {
        query.value = {
            ...(form.username ? { username: form.username } : {}),
            ...(form.email ? { email: form.email } : {})
        }
    },
    { deep: true, }
)
</script>

<template>
    <Head title="Manage Accounts" />

    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-4 flex justify-end items-center">
                    <div class="flex gap-2">
                        <button
                            @click="filterExpanded = !filterExpanded"
                            class="rounded-lg border border-gray-400 px-4 py-2 flex gap-2 items-center text-gray-500"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 13.5V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m12-3V3.75m0 9.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 3.75V16.5m-6-9V3.75m0 3.75a1.5 1.5 0 0 1 0 3m0-3a1.5 1.5 0 0 0 0 3m0 9.75V10.5" />
                            </svg>

                            Filter

                            <svg v-if="filterExpanded" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <Link
                            href="/manage/accounts/create"
                            as="button"
                            class="
                                flex items-center justify-center gap-2 px-4 py-2 rounded-lg
                                text-sm text-gray-50 bg-gray-900
                            "
                        >
                            <svg class="size-6" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                            </svg>
                            Create Account
                        </Link>
                    </div>
                </div>

                <div v-show="filterExpanded" class="p-4 flex flex-col items-center gap-4 border-t border-gray-200">
                    <div class="flex flex-col gap-1">
                        <label for="username" class="text-xs text-gray-700 font-bold">Username</label>
                        <input
                            v-model="form.username"
                            type="text"
                            id="username"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Search..."
                        >
                    </div>
                    <div class="flex flex-col gap-1">
                        <label for="email" class="text-xs text-gray-700 font-bold">Email</label>
                        <input
                            v-model="form.email"
                            type="text"
                            id="email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Search..."
                        >
                    </div>
                </div>

                <InfinitePagination
                    :initial="accounts"
                    :query="query"
                    v-slot="source"
                    class="overflow-x-auto"
                >
                    <AccountListTable
                        :accounts="source.data"
                        :class="source.loading ? 'opacity-50' : ''"
                    />
                </InfinitePagination>
            </Card>
        </div>
    </section>
</template>
