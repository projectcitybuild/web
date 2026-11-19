<script setup lang="ts">
import { Routes } from '../../../../Routes'
import { Link } from '@inertiajs/vue3'
import { format } from '../../../Utilities/DateFormatter'
import Pill from '../../../Components/Pill.vue'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
import usePermissions from '../../../Composables/usePermissions'
import LinkButton from '../../../../shared/Components/LinkButton.vue'

interface Props {
    bans: App.Http.Data.AdminGameIPBanData[],
}
const props = defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'status', label: 'Status' },
    { key: 'ip_address', label: 'IP Address' },
    { key: 'reason', label: 'Reason' },
    { key: 'created_at', label: 'Created At' },
]
const rows = computed(
    () => props.bans.map((ban) => ({
        ...ban,
        created_at: format(ban.created_at),
    }))
)
function isActive(ban: App.Http.Data.AdminGameIPBanData) {
    return ban.unbanned_at == null
}
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true">
        <template #status="{ item }">
            <Pill :variant="isActive(item) ? 'danger' : 'default'">
                {{ isActive(item) ? 'Active' : 'Expired' }}
            </Pill>
        </template>

        <template #actions="{ item }" v-if="can('web.manage.ip_bans.edit')">
            <LinkButton :href="Routes.manage.ipBans.edit(item.id)" variant="outline" size="sm">
                <SvgIcon :svg="Icons.pencil" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
