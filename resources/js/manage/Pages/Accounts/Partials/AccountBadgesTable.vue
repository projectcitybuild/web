<script setup lang="ts">
import type { Badge } from '../../../Data/Badge'
import { Link } from '@inertiajs/vue3'

interface Props {
    account_id: number,
    badges: Badge[],
}
defineProps<Props>()
</script>

<template>
    <table v-if="badges.length > 0" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Unicode Icon</th>
            <th scope="col" class="px-4 py-3">Display Name</th>
            <th scope="col" class="px-4 py-3">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="badge in badges">
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ badge.id }}</td>
            <td class="px-4 py-3">{{ badge.unicode_icon }}</td>
            <td class="px-4 py-3">{{ badge.display_name }}</td>
            <td class="px-4 py-3">
                <Link
                    as="button"
                    method="delete"
                    :href="'/manage/accounts/' + account_id + '/badges/' + badge.id"
                    class="px-2 py-1 rounded-md border border-gray-300 text-xs text-gray-400"
                >
                    Remove
                </Link>
            </td>
        </tr>
        </tbody>
    </table>
    <div v-else class="flex p-4">
        <span class="text-sm text-gray-400">No badges</span>
    </div>
</template>
