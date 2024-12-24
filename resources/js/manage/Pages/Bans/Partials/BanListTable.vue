<script setup>
import { Link } from '@inertiajs/vue3'
import { format } from 'date-fns'

const props = defineProps({
    bans: Object
})

function formatted(dateString) {
    if (!dateString) return null
    const date = new Date(dateString)
    return format(date, 'MMM do, yyyy, h:ma')
}
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Status</th>
            <th scope="col" class="px-4 py-3">Player</th>
            <th scope="col" class="px-4 py-3">Reason</th>
            <th scope="col" class="px-4 py-3">Created At</th>
            <th scope="col" class="px-4 py-3">Expires At</th>
            <th scope="col" class="px-4 py-3">
                <span class="sr-only">Actions</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="ban in bans.data">
            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ ban.id }}</th>
            <td class="px-4 py-3">
<!--                <span v-if="ban.unbanned_at == null" class="py-1 px-2 bg-gray-200 rounded-md text-xs">Expired</span>-->
                <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Active (TODO)</span>
            </td>
            <td class="px-4 py-3 font-bold">{{ ban.banned_player.alias ?? ban.banned_alias_at_time }}</td>
            <td class="px-4 py-3">{{ ban.reason }}</td>
            <td class="px-4 py-3">{{ formatted(ban.created_at) }}</td>
            <td class="px-4 py-3">{{ formatted(ban.expires_at) ?? 'Never' }}</td>
            <td class="px-4 py-3 text-right">
                <Link :href="'/manage/player-bans/' + ban.id + '/edit'" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                    Edit
                </Link>
            </td>
        </tr>
        </tbody>
    </table>
</template>
