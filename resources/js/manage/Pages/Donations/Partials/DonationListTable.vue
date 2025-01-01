<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Donation } from '../../../Data/Donation'
import { format } from '../../../Utilities/DateFormatter'

interface Props {
    donations: Donation[],
}

defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">#</th>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Account</th>
            <th scope="col" class="px-4 py-3">Amount</th>
            <th scope="col" class="px-4 py-3">Date</th>
            <th scope="col" class="px-4 py-3">
                <span class="sr-only">Actions</span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="(donation, index) in donations">
            <th scope="row" class="px-4 py-3 text-gray-400 whitespace-nowrap dark:text-white">#{{ index + 1 }}</th>
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ donation.donation_id }}</td>
            <td class="px-4 py-3">
                <Link
                    :href="'/manage/accounts/' + donation.account?.account_id"
                    class="text-blue-500"
                >
                    {{ donation.account?.username }}
                </Link>
            </td>
            <td class="px-4 py-3">{{ donation.amount }}</td>
            <td class="px-4 py-3">{{ format(donation.created_at) }}</td>
            <td class="px-4 py-3 text-right">
                <Link
                    :href="'/manage/donations/' + donation.donation_id + '/edit'"
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
