<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import { format, timeAgo } from '../../Utilities/DateFormatter'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import { Account } from '../../Data/Account'
import AccountPlayersTable from './Partials/AccountPlayersTable.vue'
import Pill from '../../Components/Pill.vue'
import AccountBadgesTable from './Partials/AccountBadgesTable.vue'
import AccountRolesTable from './Partials/AccountRolesTable.vue'
import FilledButton from '../../Components/FilledButton.vue'
import OutlinedButton from '../../Components/OutlinedButton.vue'
import ConfirmDialog from '../../Components/ConfirmDialog.vue'
import { computed, ref } from 'vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import AccountActivationsTable from './Partials/AccountActivationsTable.vue'
import AccountDonationsTable from './Partials/AccountDonationsTable.vue'
import AccountEmailChangeRequestsTable from './Partials/AccountEmailChangeRequestsTable.vue'
import AccountBanAppealsTable from './Partials/AccountBanAppealsTable.vue'
import AccountBuilderRankApplicationsTable from './Partials/AccountBuilderRankApplicationsTable.vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import Tabs from '../../Components/Tabs.vue'
import TabPanel from '../../Components/TabPanel.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'

interface Props {
    account: Account,
    success?: string,
}
const props = defineProps<Props>()

const activateModal = ref<InstanceType<typeof ConfirmDialog>>()
const deactivateModal = ref<InstanceType<typeof ConfirmDialog>>()
const disableMfaModal = ref<InstanceType<typeof ConfirmDialog>>()
const unlinkPlayerModal = ref<InstanceType<typeof ConfirmDialog>>()
const unlinkPlayerId = ref<number|null>(null)
const tabs = [
    { id: 'linked', label: 'Linked Data' },
    { id: 'security', label: 'Access and Security' },
    { id: 'applications', label: 'Applications' },
    { id: 'contributions', label: 'Contributions' },
] as const
const activeTab = ref('linked')

const { can } = usePermissions()

const initials = computed(() => props.account.username
    .split(/[\s._-]+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part.charAt(0))
    .join('')
    .toUpperCase())

function forceActivate() {
    activateModal.value?.close()
    router.post('/manage/accounts/' + props.account.id + '/activate')
}

function forceDeactivate() {
    deactivateModal.value?.close()
    router.delete('/manage/accounts/' + props.account.id + '/activate')
}

function sendVerificationEmail() {
    router.post('/manage/accounts/' + props.account.id + '/activate/send')
}

function disableMfa() {
    disableMfaModal.value?.close()
    router.delete('/manage/accounts/' + props.account.id + '/mfa')
}

function unlinkPlayer() {
    unlinkPlayerModal.value?.close()
    router.delete('/manage/accounts/' + props.account.id + '/players/' + unlinkPlayerId.value)
}

function showUnlinkDialog(playerId: number) {
    unlinkPlayerId.value = playerId
    unlinkPlayerModal.value?.open()
}
</script>

<template>
    <div class="[&_.table-frame]:border-t-0">
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
        <ConfirmDialog
            ref="unlinkPlayerModal"
            message="This will disassociate the player from this account."
            confirm-title="Yes, Proceed"
            :on-confirm="unlinkPlayer"
        />

        <PageHeader :title="account.username" description="Review account access, security, linked game data, and contributions.">
            <template #actions v-if="can('web.manage.accounts.edit')">
                <LinkButton :href="'/manage/accounts/' + account.id + '/edit'" variant="outline">
                    <SvgIcon :svg="Icons.pencil" class="size-4" aria-hidden="true" />
                    Edit Account
                </LinkButton>
            </template>
        </PageHeader>

        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card padding="none" class="mt-4">
            <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="flex size-14 shrink-0 items-center justify-center rounded-full bg-info-soft text-lg font-semibold text-info dark:text-info">
                        {{ initials }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="truncate text-xl font-semibold text-foreground">
                            {{ account.username }} <span class="font-normal text-muted">(#{{ account.id }})</span>
                        </h2>
                        <p class="mt-1 truncate text-sm text-muted dark:text-subtle">{{ account.email }}</p>
                        <div v-if="account.roles?.length" class="mt-2 flex flex-wrap gap-1.5">
                            <Pill v-for="role in account.roles" :key="role.id" variant="info" class="text-xs font-medium">
                                {{ role.name }}
                            </Pill>
                        </div>
                        <p v-else class="mt-2 text-xs text-muted dark:text-subtle">No roles assigned</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 sm:justify-end">
                    <Pill :variant="account.activated ? 'success' : 'danger'" class="flex items-center gap-1 font-semibold">
                        <SvgIcon :svg="account.activated ? Icons.check : Icons.close" class="size-4" />
                        {{ account.activated ? 'Email verified' : 'Not verified' }}
                    </Pill>
                    <Pill :variant="account.is_totp_enabled ? 'success' : 'danger'" class="flex items-center gap-1 font-semibold">
                        <SvgIcon :svg="account.is_totp_enabled ? Icons.checkShield : Icons.unlock" class="size-4" />
                        {{ account.is_totp_enabled ? '2FA enabled' : '2FA disabled' }}
                    </Pill>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 border border-surface-muted rounded-[var(--radius-md)] px-5 py-4 sm:grid-cols-3 sm:gap-6 sm:px-6">
                <div>
                    <p class="text-xs text-muted">Created</p>
                    <p class="mt-1 text-sm font-medium text-foreground">{{ format(account.created_at) }}</p>
                    <p class="mt-0.5 text-xs text-muted">{{ timeAgo(account.created_at) }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Last updated</p>
                    <p class="mt-1 text-sm font-medium text-foreground">{{ format(account.updated_at) }}</p>
                    <p class="mt-0.5 text-xs text-muted">{{ timeAgo(account.updated_at) }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted">Last login</p>
                    <p class="mt-1 text-sm font-medium text-foreground">{{ account.last_login_at ? format(account.last_login_at) : 'Never' }}</p>
                    <p v-if="account.last_login_at" class="mt-0.5 text-xs text-muted">{{ timeAgo(account.last_login_at) }}</p>
                </div>
            </div>
        </Card>

        <Tabs v-model="activeTab" :tabs="tabs" id-prefix="account" aria-label="Account sections" class="mt-5" />

        <TabPanel tab-id="security" id-prefix="account" :active-tab="activeTab" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
            <Card padding="none">
                <div class="border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Access and security</h2>
                    <p class="mt-1 text-sm text-muted dark:text-subtle">Verification and sign-in protection</p>
                </div>
                <div class="space-y-4 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-foreground">Email verification</p>
                            <p class="mt-1 text-sm text-muted dark:text-subtle">{{ account.activated ? 'This account can sign in.' : 'This account has not verified its email.' }}</p>
                        </div>
                        <div v-if="can('web.manage.accounts.edit')" class="flex flex-wrap gap-2">
                            <template v-if="!account.activated">
                                <FilledButton variant="secondary" @click="sendVerificationEmail">
                                    <SvgIcon :svg="Icons.email" />
                                    Send email
                                </FilledButton>
                                <OutlinedButton variant="danger" @click="activateModal?.open()">Force activate</OutlinedButton>
                            </template>
                            <OutlinedButton v-else variant="danger" @click="deactivateModal?.open()">Force re-verification</OutlinedButton>
                        </div>
                    </div>
                    <hr />
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-foreground">Two-factor authentication</p>
                            <p class="mt-1 text-sm text-muted dark:text-subtle">{{ account.is_totp_enabled ? 'An authenticator is configured.' : 'No authenticator is configured.' }}</p>
                        </div>
                        <OutlinedButton v-if="account.is_totp_enabled && can('web.manage.accounts.edit')" variant="danger" @click="disableMfaModal?.open()">
                            <SvgIcon :svg="Icons.unlock" />
                            Disable 2FA
                        </OutlinedButton>
                    </div>
                </div>
            </Card>

            <Card padding="none" v-if="can('web.manage.accounts.view_email')" class="xl:col-span-2">
                <div class="border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Pending email changes</h2>
                </div>
                <div class="overflow-x-auto">
                    <AccountEmailChangeRequestsTable :requests="account.email_change_requests ?? []" :account_id="account.id" />
                </div>
            </Card>

            <Card padding="none">
                <div class="border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Pending activations</h2>
                </div>
                <div class="overflow-x-auto"><AccountActivationsTable :activations="account.activations ?? []" /></div>
            </Card>
        </TabPanel>

        <TabPanel tab-id="linked" id-prefix="account" :active-tab="activeTab" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
            <Card padding="none" class="xl:col-span-2">
                <div class="flex items-center justify-between gap-4 border-b border-border p-4">
                    <div>
                        <h2 class="text-base font-semibold text-foreground">Linked players</h2>
                        <p class="mt-1 text-sm text-muted dark:text-subtle">Game profiles connected to this account</p>
                    </div>
                    <LinkButton v-if="can('web.manage.accounts.edit')" :href="'/manage/accounts/' + account.id + '/players'" variant="secondary">Add player</LinkButton>
                </div>
                <div class="overflow-x-auto">
                    <AccountPlayersTable :players="account.minecraft_account ?? []" @unlink-tap="showUnlinkDialog" />
                </div>
            </Card>

            <Card padding="none">
                <div class="flex items-center justify-between gap-4 border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Roles</h2>
                    <LinkButton v-if="can('web.manage.accounts.edit')" :href="'/manage/accounts/' + account.id + '/roles'" variant="outline">
                        <SvgIcon :svg="Icons.pencil" />
                        Edit
                    </LinkButton>
                </div>
                <div class="overflow-x-auto"><AccountRolesTable :roles="account.roles ?? []" /></div>
            </Card>

            <Card padding="none" v-if="can('web.manage.badges.view')">
                <div class="flex items-center justify-between gap-4 border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Badges</h2>
                    <LinkButton v-if="can('web.manage.accounts.edit')" :href="'/manage/accounts/' + account.id + '/badges'" variant="outline">
                        <SvgIcon :svg="Icons.pencil" />
                        Edit
                    </LinkButton>
                </div>
                <div class="overflow-x-auto"><AccountBadgesTable :badges="account.badges ?? []" /></div>
            </Card>

        </TabPanel>

        <TabPanel tab-id="applications" id-prefix="account" :active-tab="activeTab" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
            <Card padding="none">
                <div class="border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Ban appeals</h2>
                </div>
                <AccountBanAppealsTable :account_id="account.id" />
            </Card>

            <Card padding="none">
                <div class="border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Builder applications</h2>
                </div>
                <AccountBuilderRankApplicationsTable :account_id="account.id" />
            </Card>
        </TabPanel>

        <TabPanel tab-id="contributions" id-prefix="account" :active-tab="activeTab" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
            <Card padding="none" v-if="can('web.manage.donations.view')" class="xl:col-span-2">
                <div class="flex items-center justify-between gap-4 border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Donations</h2>
                    <LinkButton :href="'/manage/accounts/' + account.id + '/badges'" variant="secondary">Create</LinkButton>
                </div>
                <div class="overflow-x-auto"><AccountDonationsTable :donations="account.donations ?? []" /></div>
            </Card>
        </TabPanel>

    </div>
</template>
