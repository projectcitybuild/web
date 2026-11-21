<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import Card from '../../../../../Components/ManageCard.vue'
import ConfirmDialog from '../../../../../Components/ConfirmDialog.vue'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import usePermissions from '../../../../../Composables/usePermissions'
import { Icons } from '../../../../../Navigation/Icons'
import AccountPlayersTable from './AccountPlayersTable.vue'
import AccountRolesTable from './AccountRolesTable.vue'
import AccountBadgesTable from './AccountBadgesTable.vue'
import TabPanel from '../../../../../Components/TabPanel.vue'
import LinkButton from '../../../../../Components/LinkButton.vue'
import FilledButton from '../../../../../Components/FilledButton.vue'

interface Props {
    account: App.Http.Data.AdminAccountData,
    activeTab: string,
}
const props = defineProps<Props>()

const unlinkPlayerModal = ref<InstanceType<typeof ConfirmDialog>>()
const disconnectDiscordModal = ref<InstanceType<typeof ConfirmDialog>>()
const unlinkPlayerId = ref<number | null>(null)
const { can } = usePermissions()

function unlinkPlayer() {
    unlinkPlayerModal.value?.close()
    router.delete('/manage/accounts/' + props.account.id + '/players/' + unlinkPlayerId.value)
}

function showUnlinkDialog(playerId: number) {
    unlinkPlayerId.value = playerId
    unlinkPlayerModal.value?.open()
}

function disconnectDiscordAccount(): void {
    disconnectDiscordModal.value?.close()
    router.delete(Routes.manage.accounts.discord(props.account.id))
}

function forceSyncDiscordAccount(): void {
    router.post(Routes.manage.accounts.discordSync(props.account.id), {})
}
</script>

<template>
    <TabPanel tab-id="linked" id-prefix="account" :active-tab="props.activeTab" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
        <Card padding="none">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-border p-4">
                <div>
                    <h2 class="text-base font-semibold text-foreground">Discord account</h2>
                    <p class="mt-1 text-sm text-muted dark:text-subtle">Discord identity connected to this account</p>
                </div>
                <div
                    v-if="can('web.manage.accounts.edit') && props.account.discord_account"
                    class="flex flex-wrap justify-end gap-2"
                >
                    <FilledButton
                        variant="secondary"
                        size="sm"
                        @click="forceSyncDiscordAccount"
                    >
                        Force sync
                    </FilledButton>
                    <FilledButton
                        variant="danger"
                        size="sm"
                        @click="disconnectDiscordModal?.open()"
                    >
                        Force disconnect
                    </FilledButton>
                </div>
            </div>
            <dl v-if="props.account.discord_account" class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs text-muted">Username</dt>
                    <dd class="mt-1 text-sm font-medium text-foreground">
                        {{ props.account.discord_account.username ?? 'Unknown' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-muted">Discord user ID</dt>
                    <dd class="mt-1 break-all font-mono text-sm text-foreground">
                        {{ props.account.discord_account.provider_user_id }}
                    </dd>
                </div>
            </dl>
            <p v-else class="p-4 text-sm text-muted dark:text-subtle">No Discord account linked.</p>
        </Card>

        <Card padding="none" class="xl:col-span-2">
            <div class="flex items-center justify-between gap-4 border-b border-border p-4">
                <div>
                    <h2 class="text-base font-semibold text-foreground">Linked players</h2>
                    <p class="mt-1 text-sm text-muted dark:text-subtle">Game profiles connected to this account</p>
                </div>
                <LinkButton v-if="can('web.manage.accounts.edit')" :href="Routes.manage.accounts.players(props.account.id)" variant="outline">
                    <SvgIcon :svg="Icons.plus" />
                    Add player
                </LinkButton>
            </div>
            <div class="overflow-x-auto">
                <AccountPlayersTable :players="props.account.minecraft_account ?? []" @unlink-tap="showUnlinkDialog" />
            </div>
        </Card>

        <Card padding="none">
            <div class="flex items-center justify-between gap-4 border-b border-border p-4">
                <h2 class="text-base font-semibold text-foreground">Roles</h2>
                <LinkButton v-if="can('web.manage.accounts.edit')" :href="Routes.manage.accounts.roles(props.account.id)" variant="outline">
                    <SvgIcon :svg="Icons.pencil" />
                    Edit
                </LinkButton>
            </div>
            <div class="overflow-x-auto">
                <AccountRolesTable :roles="props.account.roles ?? []" />
            </div>
        </Card>

        <Card v-if="can('web.manage.badges.view')" padding="none">
            <div class="flex items-center justify-between gap-4 border-b border-border p-4">
                <h2 class="text-base font-semibold text-foreground">Badges</h2>
                <LinkButton v-if="can('web.manage.accounts.edit')" :href="Routes.manage.accounts.badges(props.account.id)" variant="outline">
                    <SvgIcon :svg="Icons.pencil" />
                    Edit
                </LinkButton>
            </div>
            <div class="overflow-x-auto">
                <AccountBadgesTable :badges="props.account.badges ?? []" />
            </div>
        </Card>

        <ConfirmDialog
            ref="unlinkPlayerModal"
            message="This will disassociate the player from this account."
            confirm-title="Yes, Proceed"
            :on-confirm="unlinkPlayer"
        />
        <ConfirmDialog
            ref="disconnectDiscordModal"
            title="Force disconnect Discord account?"
            message="This will disconnect the user's Discord account and remove its synced roles."
            confirm-title="Yes, disconnect"
            :on-confirm="disconnectDiscordAccount"
        />
    </TabPanel>
</template>
