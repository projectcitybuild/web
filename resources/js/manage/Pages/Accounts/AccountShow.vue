<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import { format } from '../../Utilities/DateFormatter'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import { Account } from '../../Data/Account'
import AccountPlayersTable from './Partials/AccountPlayersTable.vue'
import Pill from '../../Components/Pill.vue'
import AccountBadgesTable from './Partials/AccountBadgesTable.vue'
import AccountGroupsTable from './Partials/AccountGroupsTable.vue'
import ToolBar from '../../Components/ToolBar.vue'

interface Props {
    account: Account,
    success?: string,
}
defineProps<Props>()
</script>

<template>
    <div class="p-3 sm:p-5 mx-auto max-w-screen-xl">
        <Head :title="'Viewing Account: ' + account.username" />

        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ToolBar>
            <template v-slot:left>
                <BackButton href="/manage/accounts"/>
            </template>
            <template v-slot:right>
                <Link
                    :href="'/manage/accounts/' + account.account_id + '/edit'"
                    as="button"
                    class="
                                flex flex-row items-center justify-center gap-2 px-4 py-2 rounded-lg
                                text-sm text-white bg-blue-700
                                hover:bg-primary-800
                                focus:ring-4 focus:ring-blue-300
                                dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800
                            "
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                    </svg>
                    Edit Account
                </Link>
            </template>
        </ToolBar>

        <div class="mt-4 flex flex-col gap-4 md:flex-row">
            <section>
                <Card class="max-w-2xl">
                    <div class="px-4 py-8">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ account.username }}</h1>
                        <div class="mt-2 mb-2 text-sm text-gray-500">{{ account.email }}</div>
                    </div>
                    <div class="p-4 border-t border-gray-200">
                        <div class="space-y-4">
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Created At</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    {{ format(account.created_at) }}
                                </dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Updated At</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    {{ format(account.updated_at) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </Card>

                <Card class="max-w-2xl mt-4 p-4">
                    <h2 class="mb-4 font-bold">Statuses</h2>

                    <div class="space-y-4">
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                <Pill v-if="account.activated" variant="success" class="flex gap-1 items-center font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    Activated
                                </Pill>
                                <Pill v-else variant="danger" class="flex gap-1 items-center font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                    Not Activated
                                </Pill>
                            </dd>
                        </dl>
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">2FA</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                <Pill v-if="account.is_totp_enabled" variant="success" class="flex gap-1 items-center font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                    </svg>
                                    Enabled
                                </Pill>
                                <Pill v-else variant="danger" class="flex gap-1 items-center font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                    </svg>
                                    Not Enabled
                                </Pill>
                            </dd>
                        </dl>
                    </div>
                </Card>
            </section>

            <section class="grow">
                <Card>
                    <div class="p-4 font-bold">
                        Linked Players
                    </div>
                    <AccountPlayersTable :players="account.minecraft_account" />
                </Card>

                <Card class="mt-4">
                    <div class="p-4 flex justify-between items-center">
                        <h2 class="font-bold">Groups</h2>
                        <Link
                            :href="'/manage/accounts/' + account.account_id + '/groups'"
                            class="rounded-lg px-4 py-1 border border-gray-200 text-sm text-gray-400"
                        >
                            Edit
                        </Link>
                    </div>
                    <AccountGroupsTable :groups="account.groups" />
                </Card>

                <Card class="mt-4">
                    <div class="p-4 font-bold">
                        Pending Email Changes
                    </div>
                </Card>

                <Card class="mt-4">
                    <div class="p-4 flex justify-between items-center">
                        <h2 class="font-bold">Badges</h2>
                        <Link
                            :href="'/manage/accounts/' + account.account_id + '/badges'"
                            class="rounded-lg px-4 py-1 border border-gray-200 text-sm text-gray-400"
                        >
                            Edit
                        </Link>
                    </div>
                    <AccountBadgesTable :badges="account.badges" />
                </Card>

                <Card class="mt-4">
                    <div class="p-4 flex justify-between items-center">
                        <h2 class="font-bold">Donations</h2>
                        <Link
                            :href="'/manage/accounts/' + account.account_id + '/badges'"
                            class="rounded-lg px-4 py-1 border border-gray-200 text-sm text-gray-400"
                        >
                            Create
                        </Link>
                    </div>

                </Card>
            </section>
        </div>
    </div>
</template>
