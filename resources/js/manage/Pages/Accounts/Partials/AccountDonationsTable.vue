<script setup lang="ts">
import type { Donation } from '../../../Data/Donation'
import { format } from '../../../Utilities/DateFormatter'
import { Link } from '@inertiajs/vue3'
import FilledButton from '../../../Components/FilledButton.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'

interface Props {
    donations: Donation[],
}
defineProps<Props>()
</script>

<template>
    <table v-if="donations.length > 0" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Amount</th>
            <th scope="col" class="px-4 py-3">Date</th>
            <th scope="col" class="px-4 py-3"></th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="donation in donations">
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ donation.donation_id }}</td>
            <td class="px-4 py-3">{{ donation.amount }}</td>
            <td class="px-4 py-3">{{ format(donation.created_at) }}</td>
            <td class="px-4 py-1 flex justify-end">
                <Link :href="'/manage/donations/' + donation.donation_id + '/edit'">
                    <OutlinedButton variant="secondary">
                        Edit
                    </OutlinedButton>
                </Link>
            </td>
        </tr>
        </tbody>
    </table>
    <div v-else class="flex p-4">
        <span class="text-sm text-gray-400">No donations</span>
    </div>
</template>
