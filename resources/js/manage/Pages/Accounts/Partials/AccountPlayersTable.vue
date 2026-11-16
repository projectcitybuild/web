<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import type { Player } from '../../../Data/Player'
import MinecraftAvatar from '../../../Components/MinecraftAvatar.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    players: Player[],
}
defineProps<Props>()

defineEmits(['unlinkTap'])

const fields = [
    { key: 'id', label: 'ID' },
    { key: 'skin', label: 'Skin' },
    { key: 'uuid', label: 'UUID' },
    { key: 'alias', label: 'Alias' },
]
</script>

<template>
    <DataTable v-if="players.length > 0" :fields="fields" :rows="players">
        <template #skin="{ item }">
            <MinecraftAvatar :uuid="item.uuid" :size="32" />
        </template>
        <template #uuid="{ item }">
                <Link
                    :href="'/manage/players/' + item.id"
                    class="text-blue-500"
                >
                    {{ item.uuid }}
                </Link>
        </template>
        <template #actions="{ item }">
            <FilledButton variant="danger" size="sm" @click="$emit('unlinkTap', item.id)">
                Unlink
            </FilledButton>
        </template>
    </DataTable>
    <div v-else class="flex p-4">
        <span class="text-sm text-gray-400">No linked players</span>
    </div>
</template>
