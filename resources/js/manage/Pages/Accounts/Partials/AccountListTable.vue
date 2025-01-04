<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { format } from '../../../Utilities/DateFormatter'
import type { Account } from '../../../Data/Account'
import BooleanCheck from '../../../Components/BooleanCheck.vue'
import DataTable from '../../../Components/DataTable.vue'
import { computed } from 'vue'

interface Props {
    accounts: Account[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'username', label: 'Username' },
    { key: 'email', label: 'Email' },
    { key: 'activated', label: 'Activated' },
    { key: 'created_at', label: 'Created At' },
    { key: 'updated_at', label: 'Updated At' },
]
const rows = computed(
    () => props.accounts.map((account) => ({
        id: account.account_id,
        username: account.username,
        email: account.email,
        activated: account.activated,
        created_at: format(account.created_at),
        updated_at: format(account.updated_at),
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true">
        <template #activated="{ item }">
            <BooleanCheck :value="item.activated" />
        </template>
        <template #username="{ item }">
            <Link
                :href="'/manage/accounts/' + item.id"
                class="text-blue-500"
            >
                {{ item.username }}
            </Link>
        </template>
    </DataTable>
</template>
