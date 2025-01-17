<script setup lang="ts">
import InfinitePagination from '../../../Components/InfinitePagination.vue'
import { format } from '../../../Utilities/DateFormatter'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import { BuilderRankApplicationStatus } from '../../../Data/BuilderRankApplication'
import Pill from '../../../Components/Pill.vue'

interface Props {
    account_id: number,
}
defineProps<Props>()
</script>

<template>
    <InfinitePagination
        :path="'/manage/accounts/' + account_id + '/builder-ranks'"
        v-slot="source"
        class="overflow-x-auto border-t border-gray-200"
    >
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-4 py-3">Id</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3">Current Build Rank</th>
                <th scope="col" class="px-4 py-3">Created At</th>
                <th scope="col" class="px-4 py-3"></th>
            </tr>
            </thead>
            <tbody>
            <tr class="border-b dark:border-gray-700" v-for="application in source.data">
                <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ application.id }}</td>
                <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">
                    <Pill v-if="application.status === BuilderRankApplicationStatus.pending">Pending</Pill>
                    <Pill v-if="application.status === BuilderRankApplicationStatus.denied" variant="danger">Denied</Pill>
                    <Pill v-if="application.status === BuilderRankApplicationStatus.approved" variant="success">Approved</Pill>
                </td>
                <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ application.current_builder_rank }}</td>
                <td class="px-4 py-3">{{ format(application.created_at) }}</td>
                <td class="px-4 py-1 flex justify-end">
                    <a :href="'/review/builder-ranks/' + application.id">
                        <OutlinedButton variant="secondary">View</OutlinedButton>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </InfinitePagination>
</template>
