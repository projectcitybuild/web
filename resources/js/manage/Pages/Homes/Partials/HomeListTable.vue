<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { format } from '../../../Utilities/DateFormatter'
import MinecraftAvatar from '../../../Components/MinecraftAvatar.vue'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions'
import { Home } from '../../../Data/Home'

interface Props {
    homes: Home[],
}
const props = defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'player', label: 'Player' },
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
    () => props.homes.map((home) => ({
        ...home,
        created_at: format(home.created_at),
        updated_at: format(home.updated_at),
    }))
)
</script>

<template>
    <DataTable :fields="fields" :rows="rows" :show-index="true" class="border-t border-border">
        <template #player="{ item }">
            <MinecraftAvatar :uuid="item.player?.uuid" :size="16"/>
            <Link
                :href="'/manage/players/' + item.player?.id"
                class="text-blue-500"
            >
                {{ item.player?.alias ?? "-" }}
            </Link>
        </template>

        <template #actions="{ item }">
            <Link
                v-if="can('web.manage.homes.edit')"
                :href="'/manage/homes/' + item.id + '/edit'"
                class="inline-flex min-h-10 items-center justify-center gap-2 rounded-[var(--radius-md)] bg-surface-muted px-4 text-sm font-medium text-foreground transition-colors hover:bg-surface-hover active:bg-surface-hover focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface motion-reduce:transition-none"
            >
                Edit
            </Link>
        </template>
    </DataTable>
</template>
