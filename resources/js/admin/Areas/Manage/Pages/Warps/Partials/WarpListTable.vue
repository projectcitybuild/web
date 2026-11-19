<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import LinkButton from '../../../../../Components/LinkButton.vue'
import { format } from '../../../../../Utilities/DateFormatter'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Navigation/Icons'
import { computed } from 'vue'
import DataTable from '../../../../../Components/DataTable.vue'
import usePermissions from '../../../../../Composables/usePermissions'
import CopyLocationButton from '../../../../../Components/CopyLocationButton.vue'
import type { SortableTableProps } from '../../../../../Composables/useTableSorting'

interface Props extends SortableTableProps {
    warps: App.Http.Data.MinecraftWarpData[],
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
    <DataTable :fields="fields" :rows="rows" :show-index="true" :sort="props.sort" :direction="props.direction" :on-sort="props.onSort">
        <template #actions="{ item }">
            <div class="flex items-center justify-end gap-2">
                <CopyLocationButton
                    :x="item.x"
                    :y="item.y"
                    :z="item.z"
                    :yaw="item.yaw"
                    :pitch="item.pitch"
                />
                <LinkButton
                    v-if="can('web.manage.warps.edit')"
                    :href="Routes.manage.warps.edit(item.id)"
                    variant="outline"
                    size="sm"
                >
                    <SvgIcon :svg="Icons.pencil" aria-hidden="true" />
                    Edit
                </LinkButton>
            </div>
        </template>
    </DataTable>
</template>
