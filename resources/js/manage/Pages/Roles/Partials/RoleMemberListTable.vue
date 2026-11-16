<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Account } from '../../../Data/Account'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    accounts: Account[],
}

defineProps<Props>()

const fields = [
    { key: 'account_id', label: 'ID' },
    { key: 'username', label: 'Username' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="accounts" :show-index="true">
        <template #username="{ item }">
            <Link
                :href="'/manage/accounts/' + item.account_id"
                class="text-primary font-bold"
            >
                {{ item.username }}
            </Link>
        </template>
    </DataTable>
</template>
