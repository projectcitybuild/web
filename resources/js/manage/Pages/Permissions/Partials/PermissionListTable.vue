<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import FilledButton from '../../../Components/FilledButton.vue'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions'
import { Permission } from '../../../Data/Permission'

interface Props {
    permissions: Permission[],
}
defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="permissions">
        <template #actions="{ item }" v-if="can('web.manage.permissions.edit')">
            <Link :href="'/manage/permissions/' + item.id + '/edit'">
                <FilledButton variant="secondary">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
