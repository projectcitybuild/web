<script setup lang="ts">
import Card from '../../../../../Components/ManageCard.vue'
import TabPanel from '../../../../../Components/TabPanel.vue'
import PlayerHomeListTable from './PlayerHomeListTable.vue'
import InfinitePagination from '../../../../../Components/InfinitePagination.vue'
import usePermissions from '../../../../../Composables/usePermissions'

interface Props {
    player: App.Http.Data.MinecraftPlayerData
    activeTab: string
}

const props = defineProps<Props>()
const { can } = usePermissions()
</script>

<template>
    <TabPanel v-if="can('web.manage.homes.view')" tab-id="player-created" id-prefix="player" :active-tab="activeTab" class="mt-4">
        <Card padding="none">
            <div class="p-4 sm:px-5">
                <h2 class="text-base font-semibold text-foreground">Homes</h2>
                <p class="mt-0.5 text-sm text-muted">Saved locations belonging to this player.</p>
            </div>
            <!-- @vue-generic {App.Http.Data.MinecraftHomeData} -->
            <InfinitePagination
                :path="'/manage/players/' + player.id + '/homes'"
                v-slot="source"
                class="overflow-x-auto"
            >
                <PlayerHomeListTable :homes="source.data" />
            </InfinitePagination>
        </Card>
    </TabPanel>
</template>
