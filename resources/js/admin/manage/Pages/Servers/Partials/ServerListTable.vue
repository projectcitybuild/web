<script setup lang="ts">
import type { Server } from '../../../Data/Server'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions';
import LinkButton from '../../../../shared/Components/LinkButton.vue'

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
    <DataTable :fields="fields" :rows="servers">
        <template #actions="{ item }" v-if="can('web.manage.servers.edit')">
            <LinkButton
                :href="'/manage/servers/' + item.id + '/edit'"
                variant="outline"
                size="sm"
            >
                <SvgIcon :svg="Icons.pencil" aria-hidden="true" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
