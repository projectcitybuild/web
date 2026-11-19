<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import LinkButton from '../../../../../Components/LinkButton.vue'
import { Link } from '@inertiajs/vue3'
import { format } from '../../../../../Utilities/DateFormatter'
import Pill from '../../../../../Components/Pill.vue'
import OutlinedButton from '../../../../../Components/OutlinedButton.vue'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Navigation/Icons'
import { computed } from 'vue'
import DataTable from '../../../../../Components/DataTable.vue'
import MinecraftAvatar from '../../../../../Components/MinecraftAvatar.vue'
import usePermissions from '../../../../../Composables/usePermissions'
import type { SortableTableProps } from '../../../../../Composables/useTableSorting'

interface Props extends SortableTableProps {
    warnings: App.Http.Data.AdminPlayerWarningData[],
}
const props = defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'player', label: 'Player', sortable: false },
    { key: 'reason', label: 'Reason' },
    { key: 'created_at', label: 'Created At' },
]
const rows = computed(
    () => props.warnings.map((warning) => ({
        ...warning,
        created_at: format(warning.created_at),
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true" :sort="props.sort" :direction="props.direction" :on-sort="props.onSort">
        <template #player="{ item }">
            <div class="px-4 py-3 flex flex-row items-center gap-2">
                <MinecraftAvatar :uuid="item.warned_player.uuid" :size="16"/>
                <Link
                    :href="Routes.manage.players.show(item.warned_player.id)"
                    class="text-primary"
                >
                    {{ item.warned_player.alias ?? '-' }}
                </Link>
            </div>
        </template>

        <template #actions="{ item }">
            <LinkButton :href="Routes.manage.warnings.edit(item.id)" v-if="can('web.manage.warnings.edit')" variant="outline" size="sm">
                <SvgIcon :svg="Icons.pencil" />
                Edit
            </LinkButton>
        </template>
    </DataTable>
</template>
