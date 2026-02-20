<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Warp } from '../../../Data/Warp'
import { format } from '../../../Utilities/DateFormatter'
import FilledButton from '../../../Components/FilledButton.vue'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions'

interface Props {
    warps: Warp[],
}
const props = defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'world', label: 'World' },
    { key: 'x', label: 'x' },
    { key: 'y', label: 'y' },
    { key: 'z', label: 'z' },
    { key: 'pitch', label: 'pitch' },
    { key: 'yaw', label: 'yaw' },
    { key: 'created_at', label: 'Created At' },
    { key: 'updated_at', label: 'Updated At' },
]
const rows = computed(
    () => props.warps.map((warp) => ({
        ...warp,
        created_at: format(warp.created_at),
        updated_at: format(warp.updated_at),
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true" class="border-t border-gray-200">
        <template #actions="{ item }">
            <Link :href="'/manage/minecraft/warps/' + item.id + '/edit'" v-if="can('web.manage.warps.edit')">
                <FilledButton variant="secondary">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
