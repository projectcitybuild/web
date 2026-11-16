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
import ToolBar from '../../Components/ToolBar.vue'
import FilledButton from '../../Components/FilledButton.vue'
import OutlinedButton from '../../Components/OutlinedButton.vue'
import ConfirmDialog from '../../Components/ConfirmDialog.vue'
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import AccountActivationsTable from './Partials/AccountActivationsTable.vue'
import AccountDonationsTable from './Partials/AccountDonationsTable.vue'
import AccountEmailChangeRequestsTable from './Partials/AccountEmailChangeRequestsTable.vue'
import AccountBanAppealsTable from './Partials/AccountBanAppealsTable.vue'
import AccountBuilderRankApplicationsTable from './Partials/AccountBuilderRankApplicationsTable.vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'

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
const activeTab = ref<(typeof tabs)[number]['id']>('linked')
const tabNav = ref<HTMLElement>()
const tabButtons = ref<HTMLButtonElement[]>([])
const tabIndicatorStyle = ref({ width: '0px', transform: 'translateX(0px)' })
let tabResizeObserver: ResizeObserver | undefined

const { can } = usePermissions()

const initials = computed(() => props.account.username
    .split(/[\s._-]+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part.charAt(0))
    .join('')
    .toUpperCase())

function updateTabIndicator() {
    const activeTabIndex = tabs.findIndex((tab) => tab.id === activeTab.value)
    const activeButton = tabButtons.value[activeTabIndex]

    if (!activeButton) {
        return
    }

    tabIndicatorStyle.value = {
        width: activeButton.offsetWidth + 'px',
        transform: 'translateX(' + activeButton.offsetLeft + 'px)',
    }
}

watch(activeTab, () => {
    void nextTick(updateTabIndicator)
})

onMounted(() => {
    void nextTick(updateTabIndicator)

    if (tabNav.value) {
        tabResizeObserver = new ResizeObserver(updateTabIndicator)
        tabResizeObserver.observe(tabNav.value)
        tabButtons.value.forEach((button) => tabResizeObserver?.observe(button))
    }
})

onBeforeUnmount(() => tabResizeObserver?.disconnect())

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

        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ToolBar>
            <template v-slot:left>
                    <h1 class="text-2xl font-semibold text-foreground">Account details</h1>
            </template>
            <template v-slot:right v-if="can('web.manage.accounts.edit')">
                <Link :href="'/manage/accounts/' + account.id + '/edit'">
                    <OutlinedButton variant="primary">
                        <SvgIcon :svg="Icons.pencil" class="size-4" aria-hidden="true" />
                        Edit Account
                    </OutlinedButton>
                </Link>
            </template>
        </ToolBar>

        <Card class="mt-4">
            <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="flex size-14 shrink-0 items-center justify-center rounded-full bg-blue-100 text-lg font-semibold text-blue-800 dark:bg-blue-900/50 dark:text-blue-200">
                        {{ initials }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="truncate text-xl font-semibold text-foreground">
                            {{ account.username }} <span class="font-normal text-muted">(#{{ account.id }})</span>
                        </h2>
                        <p class="mt-1 truncate text-sm text-gray-500 dark:text-gray-400">{{ account.email }}</p>
                        <div v-if="account.roles?.length" class="mt-2 flex flex-wrap gap-1.5">
                            <Pill v-for="role in account.roles" :key="role.id" variant="info" class="text-xs font-medium">
                                {{ role.name }}
                            </Pill>
                        </div>
                        <p v-else class="mt-2 text-xs text-gray-500 dark:text-gray-400">No roles assigned</p>
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

        <div class="mt-5 border-b border-border">
            <nav ref="tabNav" class="relative flex gap-6 overflow-x-auto" role="tablist" aria-label="Account sections">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    ref="tabButtons"
                    type="button"
                    role="tab"
                    class="shrink-0 border-b-2 border-transparent px-1 pb-3 text-sm font-medium transition-colors"
                    :class="activeTab === tab.id ? 'text-primary-700 dark:text-primary-400' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'"
                    :aria-selected="activeTab === tab.id"
                    :aria-controls="'account-panel-' + tab.id"
                    :id="'account-tab-' + tab.id"
                    @click="activeTab = tab.id"
                >
                    {{ tab.label }}
                </button>
                <span
                    aria-hidden="true"
                    class="pointer-events-none absolute bottom-0 left-0 h-0.5 bg-primary-600 transition-[width,transform] duration-300 ease-in-out dark:bg-primary-400"
                    :style="tabIndicatorStyle"
                />
            </nav>
        </div>

        <div v-if="activeTab === 'security'" id="account-panel-security" role="tabpanel" aria-labelledby="account-tab-security" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
            <Card>
                <div class="border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Access and security</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Verification and sign-in protection</p>
                </div>
                <div class="space-y-4 p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Email verification</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ account.activated ? 'This account can sign in.' : 'This account has not verified its email.' }}</p>
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
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Two-factor authentication</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ account.is_totp_enabled ? 'An authenticator is configured.' : 'No authenticator is configured.' }}</p>
                        </div>
                        <OutlinedButton v-if="account.is_totp_enabled && can('web.manage.accounts.edit')" variant="danger" @click="disableMfaModal?.open()">
                            <SvgIcon :svg="Icons.unlock" />
                            Disable 2FA
                        </OutlinedButton>
                    </div>
                </div>
            </Card>

            <Card v-if="can('web.manage.accounts.view_email')" class="xl:col-span-2">
                <div class="border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Pending email changes</h2>
                </div>
                <div class="overflow-x-auto">
                    <AccountEmailChangeRequestsTable :requests="account.email_change_requests ?? []" :account_id="account.id" />
                </div>
            </Card>

            <Card>
                <div class="border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Pending activations</h2>
                </div>
                <div class="overflow-x-auto"><AccountActivationsTable :activations="account.activations ?? []" /></div>
            </Card>
        </div>

        <div v-else-if="activeTab === 'linked'" id="account-panel-linked" role="tabpanel" aria-labelledby="account-tab-linked" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
            <Card class="xl:col-span-2">
                <div class="flex items-center justify-between gap-4 border-b border-border p-4">
                    <div>
                        <h2 class="text-base font-semibold text-foreground">Linked players</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Game profiles connected to this account</p>
                    </div>
                    <Link v-if="can('web.manage.accounts.edit')" :href="'/manage/accounts/' + account.id + '/players'">
                        <FilledButton variant="secondary">Add player</FilledButton>
                    </Link>
                </div>
                <div class="overflow-x-auto">
                    <AccountPlayersTable :players="account.minecraft_account ?? []" @unlink-tap="showUnlinkDialog" />
                </div>
            </Card>

            <Card>
                <div class="flex items-center justify-between gap-4 border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Roles</h2>
                    <Link v-if="can('web.manage.accounts.edit')" :href="'/manage/accounts/' + account.id + '/roles'">
                        <OutlinedButton variant="secondary">
                            <SvgIcon :svg="Icons.pencil" />
                            Edit
                        </OutlinedButton>
                    </Link>
                </div>
                <div class="overflow-x-auto"><AccountRolesTable :roles="account.roles ?? []" /></div>
            </Card>

            <Card v-if="can('web.manage.badges.view')">
                <div class="flex items-center justify-between gap-4 border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Badges</h2>
                    <Link v-if="can('web.manage.accounts.edit')" :href="'/manage/accounts/' + account.id + '/badges'">
                        <OutlinedButton variant="secondary">
                            <SvgIcon :svg="Icons.pencil" />
                            Edit
                        </OutlinedButton>
                    </Link>
                </div>
                <div class="overflow-x-auto"><AccountBadgesTable :badges="account.badges ?? []" /></div>
            </Card>

        </div>

        <div v-else-if="activeTab === 'applications'" id="account-panel-applications" role="tabpanel" aria-labelledby="account-tab-applications" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
            <Card>
                <div class="border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Ban appeals</h2>
                </div>
                <AccountBanAppealsTable :account_id="account.id" />
            </Card>

            <Card>
                <div class="border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Builder applications</h2>
                </div>
                <AccountBuilderRankApplicationsTable :account_id="account.id" />
            </Card>
        </div>

        <div v-else-if="activeTab === 'contributions'" id="account-panel-contributions" role="tabpanel" aria-labelledby="account-tab-contributions" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
            <Card v-if="can('web.manage.donations.view')" class="xl:col-span-2">
                <div class="flex items-center justify-between gap-4 border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Donations</h2>
                    <Link :href="'/manage/accounts/' + account.id + '/badges'">
                        <FilledButton variant="secondary">Create</FilledButton>
                    </Link>
                </div>
                <div class="overflow-x-auto"><AccountDonationsTable :donations="account.donations ?? []" /></div>
            </Card>
        </div>

    </div>
</template>
