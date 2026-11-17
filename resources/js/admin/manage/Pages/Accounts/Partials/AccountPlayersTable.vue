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
    <DataTable :fields="fields" :rows="players" empty-message="No players linked to this account">
        <template #skin="{ item }">
            <MinecraftAvatar :uuid="item.uuid" :size="64" />
        </template>
        <template #uuid="{ item }">
            <Link
                :href="'/manage/players/' + item.id"
                class="text-primary"
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
</template>
