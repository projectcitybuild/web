<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { format } from '../../../Utilities/DateFormatter'
import FilledButton from '../../../Components/FilledButton.vue'
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
    <DataTable :fields="fields" :rows="rows" :show-index="true" class="border-t border-gray-200">
        <template #player="{ item }">
            <MinecraftAvatar :uuid="item.player?.uuid" :size="16"/>
            <Link
                :href="'/manage/players/' + item.player?.player_minecraft_id"
                class="text-blue-500"
            >
                {{ item.player?.alias ?? "-" }}
            </Link>
        </template>

        <template #actions="{ item }">
            <Link :href="'/manage/homes/' + item.id + '/edit'" v-if="can('web.manage.homes.edit')">
                <FilledButton variant="secondary">
                    Edit
                </FilledButton>
            </Link>
        </template>
    </DataTable>
</template>
