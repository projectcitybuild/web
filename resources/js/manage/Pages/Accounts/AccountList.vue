<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import AccountListTable from './Partials/AccountListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import type { Account } from '../../Data/Account'

interface Props {
    success?: string,
    accounts: Paginated<Account>,
}
defineProps<Props>()
</script>

<template>
    <Head title="Manage Accounts" />

    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <div class="mx-auto max-w-screen-xl">
            <Card class="overflow-hidden">
                <div
                    class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2">
                    </div>
                </div>

                <InfinitePagination
                    :initial="accounts"
                    v-slot="source"
                    class="overflow-x-auto"
                >
                    <AccountListTable :accounts="source.data"/>
                </InfinitePagination>
            </Card>
        </div>
    </section>
</template>
