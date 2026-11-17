<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import { format, timeAgo } from '../../Utilities/DateFormatter'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import type { Account } from '../../Data/Account'
import Pill from '../../Components/Pill.vue'
import { computed, onMounted, ref, watch } from 'vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import Tabs from '../../Components/Tabs.vue'
import AccountSecurityTab from './Partials/AccountSecurityTab.vue'
import AccountLinkedDataTab from './Partials/AccountLinkedDataTab.vue'
import AccountApplicationsTab from './Partials/AccountApplicationsTab.vue'
import AccountContributionsTab from './Partials/AccountContributionsTab.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'

interface Props {
    account: Account,
    success?: string,
}
const props = defineProps<Props>()

const tabs = [
    { id: 'linked', label: 'Linked Data' },
    { id: 'security', label: 'Access and Security' },
    { id: 'applications', label: 'Applications' },
    { id: 'contributions', label: 'Contributions' },
] as const
const page = usePage()
const tabFromUrl = new URLSearchParams(page.url.split('?')[1] ?? '').get('tab')
const activeTab = ref(tabs.find((tab) => tab.id === tabFromUrl)?.id ?? tabs[0].id)

const { can } = usePermissions()

function updateTabUrl(tabId: string): void {
    const url = new URL(page.url, window.location.origin)
    url.searchParams.set('tab', tabId)

    router.replace({
        url: url.pathname + url.search + url.hash,
        preserveState: true,
        preserveScroll: true,
    })
}

watch(activeTab, updateTabUrl)
onMounted(() => updateTabUrl(activeTab.value))

const initials = computed(() => props.account.username
    .split(/[\s._-]+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part.charAt(0))
    .join('')
    .toUpperCase())

</script>

<template>
    <div class="[&_.table-frame]:border-t-0">
        <Head :title="'Viewing Account: ' + account.username" />

        <PageHeader title="Account Details">
            <template #actions v-if="can('web.manage.accounts.edit')">
                <LinkButton :href="'/manage/accounts/' + account.id + '/edit'" variant="outline">
                    <SvgIcon :svg="Icons.pencil" class="size-4" aria-hidden="true" />
                    Edit Account
                </LinkButton>
            </template>
        </PageHeader>

        <SuccessAlert v-if="success" :message="success" class="my-4"/>

        <Card padding="none">
            <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between">
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
                        <p v-else class="mt-2 text-xs text-muted dark:text-subtle">Member</p>
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

        <Tabs
            v-model="activeTab"
            :tabs="tabs"
            id-prefix="account"
            aria-label="Account sections" class="mt-5"
        />
        <AccountLinkedDataTab :account="account" :active-tab="activeTab" />
        <AccountSecurityTab :account="account" :active-tab="activeTab" />
        <AccountApplicationsTab :account="account" :active-tab="activeTab" />
        <AccountContributionsTab :account="account" :active-tab="activeTab" />

    </div>
</template>
