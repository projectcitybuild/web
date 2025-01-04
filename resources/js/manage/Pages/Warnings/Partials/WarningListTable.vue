<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { PlayerWarning } from '../../../Data/PlayerWarning'
import { format } from '../../../Utilities/DateFormatter'
import Pill from '../../../Components/Pill.vue'
import FilledButton from '../../../Components/FilledButton.vue'

interface Props {
    warnings: PlayerWarning[],
}

defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">#</th>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Weight</th>
            <th scope="col" class="px-4 py-3">Player</th>
            <th scope="col" class="px-4 py-3">Created At</th>
            <th scope="col" class="px-4 py-3">Reason</th>
            <th scope="col" class="px-4 py-3">
                <span class="sr-only">Actions</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="(warning, index) in warnings">
            <th scope="row" class="px-4 py-3 text-gray-400 whitespace-nowrap dark:text-white">#{{ index + 1 }}</th>
            <td class="px-4 py-3">{{ warning.id }}</td>
            <td class="px-4 py-3">
                <Pill>{{ warning.weight }}</Pill>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-gray-900 dark:text-white">{{ warning.warned_player.alias }}</td>
            <td class="px-4 py-3">{{ format(warning.created_at) }}</td>
            <td class="px-4 py-3">{{ warning.reason }}</td>
            <td class="px-4 py-1 text-right">
                <Link :href="'/manage/warnings/' + warning.id + '/edit'">
                    <FilledButton variant="secondary">
                        Edit
                    </FilledButton>
                </Link>
            </td>
        </tr>
        </tbody>
    </table>
</template>
