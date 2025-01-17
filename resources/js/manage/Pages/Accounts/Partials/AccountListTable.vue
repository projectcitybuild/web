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
    { key: 'account_id', label: 'ID' },
    { key: 'username', label: 'Username' },
    { key: 'email', label: 'Email' },
    { key: 'activated', label: 'Activated' },
    { key: 'has_player', label: 'Has Player' },
    { key: 'created_at', label: 'Created At' },
    { key: 'updated_at', label: 'Updated At' },
]
const rows = computed(
    () => props.accounts.map((account) => ({
        ...account,
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

        <template #has_player="{ item }">
            <BooleanCheck :value="item.minecraft_account.length > 0" />
        </template>

        <template #username="{ item }">
            <Link
                :href="'/manage/accounts/' + item.account_id"
                class="text-blue-500"
            >
                {{ item.username }}
            </Link>
        </template>
    </DataTable>
</template>
