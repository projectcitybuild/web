<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import { Link } from '@inertiajs/vue3'
import DataTable from '../../../../../Components/DataTable.vue'
import type { SortableTableProps } from '../../../../../Composables/useTableSorting'

interface Props extends SortableTableProps {
    accounts: App.Http.Data.AdminAccountData[],
}

const props = defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'username', label: 'Username' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="props.accounts" :show-index="true" :sort="props.sort" :direction="props.direction" :on-sort="props.onSort">
        <template #username="{ item }">
            <Link
                :href="Routes.manage.accounts.show(item.id)"
                class="text-primary font-bold"
            >
                {{ item.username }}
            </Link>
        </template>
    </DataTable>
</template>
