<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Account } from '../../../Data/Account'

interface Props {
    accounts: Account[],
}

defineProps<Props>()
</script>

<template>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-4 py-3">#</th>
            <th scope="col" class="px-4 py-3">Id</th>
            <th scope="col" class="px-4 py-3">Username</th>
        </tr>
        </thead>
        <tbody>
        <tr class="border-b dark:border-gray-700" v-for="(account, index) in accounts">
            <th scope="row" class="px-4 py-3 text-gray-400 whitespace-nowrap dark:text-white">#{{ index + 1 }}</th>
            <td class="px-4 py-3 text-gray-900 whitespace-nowrap dark:text-white">{{ account.account_id }}</td>
            <td class="px-4 py-3">
                <Link
                    :href="'/manage/accounts/' + account.account_id"
                    class="text-blue-500 font-bold"
                >
                    {{ account.username }}
                </Link>
            </td>
        </tr>
        </tbody>
    </table>
</template>
