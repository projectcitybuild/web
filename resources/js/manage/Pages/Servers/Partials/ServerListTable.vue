<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import type { Server } from '../../../Data/Server'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions';

interface Props {
    servers: Server[],
}
defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'ip', label: 'IP Address' },
    { key: 'port', label: 'Port' },
    { key: 'web_port', label: 'Web Port' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="servers" class="border-t border-border">
        <template #actions="{ item }" v-if="can('web.manage.servers.edit')">
            <Link :href="'/manage/servers/' + item.id + '/edit'">
                <OutlinedButton variant="secondary" size="sm">
                    <SvgIcon :svg="Icons.pencil" />
                    Edit
                </OutlinedButton>
            </Link>
        </template>
    </DataTable>
</template>
