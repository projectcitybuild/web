<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { format } from '../../../Utilities/DateFormatter'
import MinecraftAvatar from '../../../Components/MinecraftAvatar.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { computed } from 'vue'
import DataTable from '../../../Components/DataTable.vue'
import usePermissions from '../../../Composables/usePermissions'
import { Icons } from '../../../Icons'
import LinkButton from '../../../../shared/Components/LinkButton.vue'
import CopyLocationButton from '../../../Components/CopyLocationButton.vue'

interface Props {
    homes: App.Http.Data.MinecraftHomeData[],
}
const props = defineProps<Props>()

const { can } = usePermissions()

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'name', label: 'Name' },
    { key: 'player', label: 'Player' },
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
    <DataTable :fields="fields" :rows="rows" :show-index="true">
        <template #player="{ item }">
            <div class="flex flex-row items-center gap-3">
                <MinecraftAvatar v-if="item.player?.uuid" :uuid="item.player.uuid" :size="32"/>
                <Link
                    :href="'/manage/players/' + item.player?.id"
                    class="text-primary font-semibold underline"
                >
                    {{ item.player?.alias ?? "-" }}
                </Link>
            </div>
        </template>

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
                    v-if="can('web.manage.homes.edit')"
                    :href="'/manage/homes/' + item.id + '/edit'"
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
