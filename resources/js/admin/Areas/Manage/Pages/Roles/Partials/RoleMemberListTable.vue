<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import { Link } from '@inertiajs/vue3'
import DataTable from '../../../../../Components/DataTable.vue'

interface Props {
    accounts: App.Http.Data.AdminAccountData[],
}

defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'username', label: 'Username' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="accounts" :show-index="true">
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
