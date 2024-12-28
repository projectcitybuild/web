<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { compareAsc, format } from 'date-fns'
import MinecraftAvatar from '../../../Components/MinecraftAvatar.vue'
import { IPBan } from '../../../Data/IPBan'

interface Props {
    bans: IPBan[],
}
const props = defineProps<Props>()

function isActive(ban: PlayerBan) {
    return ban.unbanned_at == null
}
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">#</th>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Status</th>
            <th scope="col" class="px-4 py-3">IP Address</th>
            <th scope="col" class="px-4 py-3">Reason</th>
            <th scope="col" class="px-4 py-3">Created At</th>
            <th scope="col" class="px-4 py-3">
                <span class="sr-only">Actions</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="(ban, index) in bans">
            <th scope="row" class="px-4 py-3 text-gray-400 whitespace-nowrap dark:text-white">#{{ index + 1 }}</th>
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ ban.id }}</td>
            <td class="px-4 py-3">
                <span v-if="isActive(ban)" class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">Active</span>
                <span v-else class="py-1 px-2 bg-gray-200 rounded-md text-xs">Inactive</span>
            </td>
            <td class="px-4 py-3 font-bold flex flex-row items-center gap-2">
                {{ ban.ip_address }}
            </td>
            <td class="px-4 py-3">{{ ban.reason }}</td>
            <td class="px-4 py-3">{{ ban.created_at }}</td>
            <td class="px-4 py-3 text-right">
                <Link
                    :href="'/manage/ip-bans/' + ban.id + '/edit'"
                    class="
                        py-2 px-4 rounded-md
                        bg-gray-500 text-white
                        hover:bg-gray-600
                    "
                >
                    Edit
                </Link>
            </td>
        </tr>
        </tbody>
    </table>
</template>
