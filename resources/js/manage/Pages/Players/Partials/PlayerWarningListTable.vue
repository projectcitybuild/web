<script setup lang="ts">
import { format } from '../../../Utilities/DateFormatter'
import { Link } from '@inertiajs/vue3'
import { Warning } from 'postcss'
import FilledButton from '../../../Components/FilledButton.vue'

interface Props {
    warnings: Warning[],
}
defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Reason</th>
            <th scope="col" class="px-4 py-3">Created At</th>
            <th scope="col" class="px-4 py-3">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="warning in warnings">
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ warning.id }}</td>
            <td class="px-4 py-3">{{ warning.reason }}</td>
            <td class="px-4 py-3">{{ format(warning.created_at) }}</td>
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
