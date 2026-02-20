<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import type { Server } from '../../../Data/Server'
import FilledButton from '../../../Components/FilledButton.vue'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions';

interface Props {
    servers: Server[],
}
defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'server_id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'ip', label: 'IP Address' },
    { key: 'port', label: 'Port' },
    { key: 'web_port', label: 'Web Port' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="servers" class="border-t border-gray-200">
        <template #actions="{ item }" v-if="can('web.manage.servers.edit')">
            <Link :href="'/manage/servers/' + item.server_id + '/edit'">
                <FilledButton variant="secondary">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
