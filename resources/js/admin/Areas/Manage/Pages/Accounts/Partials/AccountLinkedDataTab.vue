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

interface Props {
    account: App.Http.Data.AdminAccountData,
    activeTab: string,
}
const props = defineProps<Props>()

const unlinkPlayerModal = ref<InstanceType<typeof ConfirmDialog>>()
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
</script>

<template>
    <TabPanel tab-id="linked" id-prefix="account" :active-tab="props.activeTab" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
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
    </TabPanel>
</template>
