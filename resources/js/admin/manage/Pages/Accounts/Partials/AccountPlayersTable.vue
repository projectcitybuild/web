<script setup lang="ts">
import { Routes } from '../../../../Routes'
import { Link } from '@inertiajs/vue3'
import MinecraftAvatar from '../../../Components/MinecraftAvatar.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import DataTable from '../../../Components/DataTable.vue'

interface Props {
    players: App.Http.Data.MinecraftPlayerData[],
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
                :href="Routes.manage.players.show(item.id)"
                class="text-primary font-semibold underline"
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
