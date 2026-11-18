<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { format } from '../../../Utilities/DateFormatter'
import BooleanCheck from '../../../Components/BooleanCheck.vue'
import DataTable from '../../../Components/DataTable.vue'
import { computed } from 'vue'

interface Props {
    accounts: App.Http.Data.AdminAccountData[],
}
const props = defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
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
            <BooleanCheck :value="(item.minecraft_account?.length ?? 0) > 0" />
        </template>

        <template #username="{ item }">
            <Link
                :href="'/manage/accounts/' + item.id"
                class="font-medium text-primary-active underline-offset-2 font-semibold underline"
            >
                {{ item.username }}
            </Link>
        </template>
    </DataTable>
</template>
