<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
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
import FilledButton from '../../Components/FilledButton.vue'
import OutlinedButton from '../../Components/OutlinedButton.vue'
import ConfirmDialog from '../../Components/ConfirmDialog.vue'
import { ref } from 'vue'
import axios from 'axios'
import SvgIcon from '../../Components/SvgIcon.vue'
import AccountActivationsTable from './Partials/AccountActivationsTable.vue'
import AccountDonationsTable from './Partials/AccountDonationsTable.vue'

interface Props {
    account: Account,
    success?: string,
}
const props = defineProps<Props>()

const activateModal = ref<InstanceType<typeof ConfirmDialog>>()
const deactivateModal = ref<InstanceType<typeof ConfirmDialog>>()
const disableMfaModal = ref<InstanceType<typeof ConfirmDialog>>()

function forceActivate() {
    activateModal.value?.close()
    router.post('/manage/accounts/' + props.account.account_id + '/activate')
}

function forceDeactivate() {
    deactivateModal.value?.close()
    router.delete('/manage/accounts/' + props.account.account_id + '/activate')
}

function sendVerificationEmail() {
    router.post('/manage/accounts/' + props.account.account_id + '/activate/send')
}

function disableMfa() {
    disableMfaModal.value?.close()
    router.delete('/manage/accounts/' + props.account.account_id + '/mfa')
}
</script>

<template>
    <div>
        <Head :title="'Viewing Account: ' + account.username" />

        <ConfirmDialog
            ref="activateModal"
            message="This will allow the user to login without having verified their email address. We won't be able to guarantee it's valid and belongs to them."
            confirm-title="Yes, Proceed"
            :on-confirm="forceActivate"
        />
        <ConfirmDialog
            ref="deactivateModal"
            message="This will prevent the user from logging-in until they reverify their email address. Note: this will NOT send them the verification email."
            confirm-title="Yes, Proceed"
            :on-confirm="forceDeactivate"
        />
        <ConfirmDialog
            ref="disableMfaModal"
            message="This will remove Two-Factor Authentication on the account."
            confirm-title="Yes, Disable It"
            :on-confirm="disableMfa"
        />

        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ToolBar>
            <template v-slot:left>
                <BackButton href="/manage/accounts"/>
            </template>
            <template v-slot:right>
                <Link :href="'/manage/accounts/' + account.account_id + '/edit'">
                    <FilledButton variant="primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                        </svg>
                        Edit Account
                    </FilledButton>
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
                    <h2 class="mb-4 font-bold">Email Verification</h2>

                    <div class="space-y-4">
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                <Pill v-if="account.activated" variant="success" class="flex gap-1 items-center font-bold">
                                    <SvgIcon icon="check" :thickness="3" class="size-4" />
                                    Activated
                                </Pill>
                                <Pill v-else variant="danger" class="flex gap-1 items-center font-bold">
                                    <SvgIcon icon="close" :thickness="3" class="size-4" />
                                    Not Activated
                                </Pill>
                            </dd>
                        </dl>

                        <hr />

                        <div class="flex flex-col gap-2">
                            <template v-if="!account.activated">
                                <FilledButton variant="secondary" @click="sendVerificationEmail">
                                    <SvgIcon icon="email" />
                                    Send Activation Email
                                </FilledButton>

                                <OutlinedButton variant="danger" @click="activateModal?.open()">
                                    Force Activate
                                </OutlinedButton>
                            </template>

                            <OutlinedButton v-else variant="danger" @click="deactivateModal?.open()">
                                Force Re-Verification
                            </OutlinedButton>
                        </div>
                    </div>
                </Card>

                <Card class="max-w-2xl mt-4 p-4">
                    <h2 class="mb-4 font-bold">2FA</h2>

                    <div class="space-y-4">
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                <Pill v-if="account.is_totp_enabled" variant="success" class="flex gap-1 items-center font-bold">
                                    <SvgIcon icon="check-shield" :thickness="3" class="size-4" />
                                    Enabled
                                </Pill>
                                <Pill v-else variant="danger" class="flex gap-1 items-center font-bold">
                                    <SvgIcon icon="unlock" :thickness="3" class="size-4" />
                                    Not Enabled
                                </Pill>
                            </dd>
                        </dl>

                        <template v-if="account.is_totp_enabled">
                            <hr />

                            <div class="flex flex-col gap-2">
                                <OutlinedButton variant="danger" @click="disableMfaModal?.open()">
                                    <SvgIcon icon="unlock" />
                                    Disable
                                </OutlinedButton>
                            </div>
                        </template>
                    </div>
                </Card>
            </section>

            <section class="grow">
                <Card>
                    <div class="p-4 font-bold">
                        Linked Players
                    </div>
                    <AccountPlayersTable :players="account.minecraft_account ?? []" />
                </Card>

                <Card class="mt-4">
                    <div class="p-4 flex justify-between items-center">
                        <h2 class="font-bold">Groups</h2>
                        <Link :href="'/manage/accounts/' + account.account_id + '/groups'">
                            <FilledButton variant="secondary">Edit</FilledButton>
                        </Link>
                    </div>
                    <AccountGroupsTable :groups="account.groups ?? []" />
                </Card>

                <Card class="mt-4">
                    <div class="p-4 flex justify-between items-center">
                        <h2 class="font-bold">Badges</h2>
                        <Link :href="'/manage/accounts/' + account.account_id + '/badges'">
                            <FilledButton variant="secondary">Edit</FilledButton>
                        </Link>
                    </div>
                    <AccountBadgesTable :badges="account.badges ?? []" />
                </Card>

                <Card class="mt-4">
                    <div class="p-4 flex justify-between items-center">
                        <h2 class="font-bold">Donations</h2>
                        <Link :href="'/manage/accounts/' + account.account_id + '/badges'">
                            <FilledButton variant="secondary">Create</FilledButton>
                        </Link>
                    </div>
                    <AccountDonationsTable :donations="account.donations ?? []" />
                </Card>

                <Card class="mt-4">
                    <div class="p-4 font-bold">
                        Pending Email Changes
                    </div>
                </Card>

                <Card class="mt-4">
                    <div class="p-4 font-bold">
                        Pending Activations
                    </div>
                    <AccountActivationsTable :activations="account.activations ?? []" />
                </Card>
            </section>
        </div>
    </div>
</template>
