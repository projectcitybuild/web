<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { IPBan } from '../../../Data/IPBan'
import { format } from '../../../Utilities/DateFormatter'
import Pill from '../../../Components/Pill.vue'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'
import FilledButton from '../../../Components/FilledButton.vue'

interface Props {
    bans: IPBan[],
}
const props = defineProps<Props>()

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
function isActive(ban: IPBan) {
    return ban.unbanned_at == null
}
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true" class="border-t border-gray-200">
        <template #status="{ item }">
            <Pill :variant="isActive(item) ? 'danger' : 'default'">
                {{ isActive(item) ? 'Active' : 'Expired' }}
            </Pill>
        </template>

        <template #actions="{ item }">
            <Link :href="'/manage/ip-bans/' + item.id + '/edit'">
                <FilledButton variant="secondary">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
