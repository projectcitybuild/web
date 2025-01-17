<script setup lang="ts">
import InfinitePagination from '../../../Components/InfinitePagination.vue'
import { format } from '../../../Utilities/DateFormatter'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import { BanAppealStatus } from '../../../Data/BanAppealStatus'
import Pill from '../../../Components/Pill.vue'

interface Props {
    account_id: number,
}
defineProps<Props>()
</script>

<template>
    <InfinitePagination
        :path="'/manage/accounts/' + account_id + '/ban-appeals'"
        v-slot="source"
        class="overflow-x-auto border-t border-gray-200"
    >
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-4 py-3">Id</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3">Created At</th>
                <th scope="col" class="px-4 py-3"></th>
            </tr>
            </thead>
            <tbody>
            <tr class="border-b dark:border-gray-700" v-for="appeal in source.data">
                <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ appeal.id }}</td>
                <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">
                    <Pill v-if="appeal.status === BanAppealStatus.pending">Pending</Pill>
                    <Pill v-if="appeal.status === BanAppealStatus.denied" variant="danger">Denied</Pill>
                    <Pill v-if="appeal.status === BanAppealStatus.unbanned" variant="success">Unbanned</Pill>
                </td>
                <td class="px-4 py-3">{{ format(appeal.created_at) }}</td>
                <td class="px-4 py-1 flex justify-end">
                    <a :href="'/review/ban-appeals/' + appeal.id">
                        <OutlinedButton variant="secondary">View</OutlinedButton>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </InfinitePagination>
</template>
