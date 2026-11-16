<script setup lang="ts">
import type { Role } from '../../../Data/Role'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    roles: Role[],
}
defineProps<Props>()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'role_type', label: 'Role Type' },
]
</script>

<template>
    <DataTable v-if="roles.length > 0" :fields="fields" :rows="roles" />
    <div v-else class="flex p-4">
        <span class="text-sm text-subtle">No roles</span>
    </div>
</template>
