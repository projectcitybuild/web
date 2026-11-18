<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Card from '../../../Components/Card.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
import usePermissions from '../../../Composables/usePermissions'
import AccountActivationsTable from './AccountActivationsTable.vue'
import AccountEmailChangeRequestsTable from './AccountEmailChangeRequestsTable.vue'
import TabPanel from '../../../Components/TabPanel.vue'

interface Props {
    account: App.Http.Data.AdminAccountData,
    activeTab: string,
}
const props = defineProps<Props>()

const activateModal = ref<InstanceType<typeof ConfirmDialog>>()
const deactivateModal = ref<InstanceType<typeof ConfirmDialog>>()
const disableMfaModal = ref<InstanceType<typeof ConfirmDialog>>()
const { can } = usePermissions()

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
</script>

<template>
    <TabPanel tab-id="security" id-prefix="account" :active-tab="props.activeTab" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
        <Card padding="none">
            <div class="border-b border-border p-4">
                <h2 class="text-base font-semibold text-foreground">Security</h2>
            </div>
            <div class="space-y-4 p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-foreground">
                            Email verification
                        </p>
                        <p class="mt-1 text-sm text-muted dark:text-subtle">
                            {{ props.account.activated ? 'This account can sign in.' : 'This account has not verified its email.' }}
                        </p>
                    </div>
                    <div v-if="can('web.manage.accounts.edit')" class="flex flex-wrap gap-2">
                        <template v-if="!props.account.activated">
                            <FilledButton variant="secondary" @click="sendVerificationEmail">
                                <SvgIcon :svg="Icons.email" />
                                Send email
                            </FilledButton>
                            <OutlinedButton variant="danger" @click="activateModal?.open()">Force activate</OutlinedButton>
                        </template>
                        <OutlinedButton v-else variant="danger" @click="deactivateModal?.open()">Force re-verification</OutlinedButton>
                    </div>
                </div>
                <div class="flex flex-col gap-3 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium text-foreground">
                            Two-factor authentication
                        </p>
                        <p class="mt-1 text-sm text-muted dark:text-subtle">
                            {{ props.account.is_totp_enabled ? 'An authenticator is configured.' : 'No authenticator is configured.' }}
                        </p>
                    </div>
                    <OutlinedButton v-if="props.account.is_totp_enabled && can('web.manage.accounts.edit')" variant="danger" @click="disableMfaModal?.open()">
                        <SvgIcon :svg="Icons.unlock" />
                        Disable 2FA
                    </OutlinedButton>
                </div>
            </div>
        </Card>

        <div class="grid grid-cols-1 gap-4">
            <Card v-if="can('web.manage.accounts.view_email')" padding="none">
                <div class="border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Pending email changes</h2>
                </div>
                <div class="overflow-x-auto">
                    <AccountEmailChangeRequestsTable :requests="props.account.email_change_requests ?? []" :account_id="props.account.id" />
                </div>
            </Card>

            <Card padding="none">
                <div class="border-b border-border p-4">
                    <h2 class="text-base font-semibold text-foreground">Pending activations</h2>
                </div>
                <div class="overflow-x-auto">
                    <AccountActivationsTable :activations="props.account.activations ?? []" />
                </div>
            </Card>
        </div>

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
    </TabPanel>
</template>
