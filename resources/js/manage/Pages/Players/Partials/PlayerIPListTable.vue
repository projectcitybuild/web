<script setup lang="ts">
import { format } from '../../../Utilities/DateFormatter'
import { Link } from '@inertiajs/vue3'
import { Warning } from 'postcss'
import FilledButton from '../../../Components/FilledButton.vue'
import { PlayerIP } from '../../../Data/PlayerIP'

interface Props {
    ips: PlayerIP[],
}
defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">IP Address</th>
            <th scope="col" class="px-4 py-3">Last Used</th>
            <th scope="col" class="px-4 py-3">First Used</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="ip in ips">
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ ip.id }}</td>
            <td class="px-4 py-3">{{ ip.ip }}</td>
            <td class="px-4 py-3">{{ format(ip.updated_at) }}</td>
            <td class="px-4 py-3">{{ format(ip.created_at) }}</td>
        </tr>
        </tbody>
    </table>
</template>
