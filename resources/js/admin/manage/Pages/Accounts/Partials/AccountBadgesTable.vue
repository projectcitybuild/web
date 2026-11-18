<script setup lang="ts">
import BooleanCheck from '../../../Components/BooleanCheck.vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    badges: App.Http.Data.AdminAccountBadgeData[],
}
defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'display_name', label: 'Display Name' },
    { key: 'unicode_icon', label: 'Unicode Icon' },
]
</script>

<template>
    <DataTable
        :fields="fields"
        :rows="badges"
        empty-message="No badges assigned"
    />
</template>
