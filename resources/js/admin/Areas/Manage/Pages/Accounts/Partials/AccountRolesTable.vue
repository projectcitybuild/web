<script setup lang="ts">
import DataTable from '../../../../../Components/DataTable.vue'

interface Props {
    roles: App.Http.Data.AdminAccountRoleData[],
}
defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'role_type', label: 'Role Type' },
]
</script>

<template>
    <DataTable
        :fields="fields"
        :rows="roles"
        empty-message="No roles assigned"
    />
</template>
