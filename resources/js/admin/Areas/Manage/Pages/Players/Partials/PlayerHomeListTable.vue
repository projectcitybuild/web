<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import { format } from '../../../../../Utilities/DateFormatter'
import usePermissions from '../../../../../Composables/usePermissions'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Navigation/Icons'
import LinkButton from '../../../../../Components/LinkButton.vue'
import DataTable from '../../../../../Components/DataTable.vue'
import CopyLocationButton from '../../../../../Components/CopyLocationButton.vue'

interface Props {
    homes: App.Http.Data.MinecraftHomeData[],
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
    { key: 'pitch', label: 'Pitch' },
    { key: 'yaw', label: 'Yaw' },
    { key: 'created_at', label: 'Created At' },
]
</script>

<template>
    <DataTable :fields="fields" :rows="props.homes">
        <template #x="{ item }">
            {{ Math.round(item.x) }}
        </template>
        <template #y="{ item }">
            {{ Math.round(item.y) }}
        </template>
        <template #z="{ item }">
            {{ Math.round(item.z) }}
        </template>
        <template #pitch="{ item }">
            {{ Number(item.pitch).toFixed(2) }}
        </template>
        <template #yaw="{ item }">
            {{ Number(item.yaw).toFixed(2) }}
        </template>
        <template #created_at="{ item }">
            {{ format(item.created_at) }}
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
                    :href="Routes.manage.homes.edit(item.id)"
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
