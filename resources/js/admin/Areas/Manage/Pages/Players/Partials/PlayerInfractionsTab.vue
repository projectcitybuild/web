<script setup lang="ts">
import Card from '../../../../../Components/ManageCard.vue'
import TabPanel from '../../../../../Components/TabPanel.vue'
import PlayerBanListTable from './PlayerBanListTable.vue'
import PlayerWarningListTable from './PlayerWarningListTable.vue'
import InfinitePagination from '../../../../../Components/InfinitePagination.vue'
import LinkButton from '../../../../../Components/LinkButton.vue'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Navigation/Icons'
import { Routes } from '../../../../../Navigation/Routes'
import usePermissions from '../../../../../Composables/usePermissions'

interface Props {
    player: App.Http.Data.MinecraftPlayerData
    activeTab: string
}

const props = defineProps<Props>()
const { can } = usePermissions()
</script>

<template>
    <TabPanel tab-id="infractions" id-prefix="player" :active-tab="activeTab" class="mt-4 space-y-4">
        <Card v-if="can('web.manage.uuid_bans.view')" padding="none">
            <div class="flex items-center justify-between gap-4 p-4 sm:px-5">
                <div>
                    <h2 class="text-base font-semibold text-foreground">Player bans</h2>
                    <p class="mt-0.5 text-sm text-muted">Restrictions associated with this player.</p>
                </div>
                <LinkButton :href="Routes.manage.playerBans.createForPlayer(player.uuid)" variant="outline">
                    <SvgIcon :svg="Icons.plus" class="size-4" aria-hidden="true" />
                    Create
                </LinkButton>
            </div>
            <!-- @vue-generic {App.Http.Data.AdminGamePlayerBanData} -->
            <InfinitePagination
                :path="'/manage/players/' + player.id + '/bans'"
                v-slot="source"
                class="overflow-x-auto"
            >
                <PlayerBanListTable :bans="source.data"/>
            </InfinitePagination>
        </Card>

        <Card v-if="can('web.manage.warnings.view')" padding="none">
            <div class="flex items-center justify-between gap-4 p-4 sm:px-5">
                <div>
                    <h2 class="text-base font-semibold text-foreground">Player warnings</h2>
                    <p class="mt-0.5 text-sm text-muted">Moderation notes recorded for this player.</p>
                </div>
                <LinkButton :href="Routes.manage.warnings.createForPlayer(player.uuid)" variant="outline">
                    <SvgIcon :svg="Icons.plus" class="size-4" aria-hidden="true" />
                    Create
                </LinkButton>
            </div>
            <!-- @vue-generic {App.Http.Data.AdminPlayerWarningData} -->
            <InfinitePagination
                :path="'/manage/players/' + player.id + '/warnings'"
                v-slot="source"
                class="overflow-x-auto"
            >
                <PlayerWarningListTable :warnings="source.data"/>
            </InfinitePagination>
        </Card>
    </TabPanel>
</template>
