<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import AccountListTable from './Partials/AccountListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import type { Account } from '../../Data/Account'
import { watch, ref } from 'vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import OutlinedButton from '../../Components/OutlinedButton.vue'

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
            ...(form.email ? { email: form.email } : {}),
            ...(form.activated != null ? { activated: form.activated } : {}),
        }
    },
    { deep: true, }
)
</script>

<template>
    <Head title="Manage Accounts" />

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <div class="flex flex-col md:flex-row md:items-start gap-4">
            <Card v-show="filterExpanded" class="p-4 flex flex-col gap-4">
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
                <div class="flex flex-col gap-1">
                    <label for="activated" class="text-xs text-gray-700 font-bold">Activated</label>
                    <select
                        v-model="form.activated"
                        id="activated"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                        <option :value="null">Any</option>
                        <option :value="1">Yes</option>
                        <option :value="0">No</option>
                    </select>
                </div>
            </Card>

            <Card>
                <div class="p-4 flex justify-end items-center">
                    <div class="flex gap-2">
                        <OutlinedButton
                            variant="secondary"
                            @click="filterExpanded = !filterExpanded"
                        >
                            <SvgIcon icon="filter" />

                            Filter

                            <SvgIcon :icon="filterExpanded ? 'chevron-up' : 'chevron-down'" />
                        </OutlinedButton>

                        <Link
                            href="/manage/accounts/create"
                            as="button"
                        >
                            <FilledButton variant="primary">
                                <SvgIcon icon="plus" />
                                Create Account
                            </FilledButton>
                        </Link>
                    </div>
                </div>

                <InfinitePagination
                    :initial="accounts"
                    :query="query"
                    v-slot="source"
                    class="overflow-x-auto border-t border-gray-200"
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
