<script setup lang="ts">
import Card from '../../../../../Components/ManageCard.vue'
import TabPanel from '../../../../../Components/TabPanel.vue'
import PlayerAuditListTable from './PlayerAuditListTable.vue'
import InfinitePagination from '../../../../../Components/InfinitePagination.vue'
import ErrorAlert from '../../../../../Components/ErrorAlert.vue'

interface Props {
    player: App.Http.Data.MinecraftPlayerData
    activeTab: string
}

const props = defineProps<Props>()
</script>

<template>
    <TabPanel tab-id="auditing" id-prefix="player" :active-tab="activeTab" class="mt-4">
        <Card padding="none">
            <div class="p-4 sm:px-5">
                <h2 class="text-base font-semibold text-foreground">Audit log</h2>
                <p class="mt-0.5 text-sm text-muted">Changes made by or to this player.</p>
            </div>
            <!-- @vue-generic {App.Http.Data.AdminAuditListEntryData} -->
            <InfinitePagination
                :path="'/manage/players/' + player.id + '/audits'"
                v-slot="source"
                class="overflow-x-auto"
            >
                <ErrorAlert v-if="source.forbidden" :errors="['You do not have permission to view audits.']" />
                <PlayerAuditListTable v-else :audits="source.data" />
            </InfinitePagination>
        </Card>
    </TabPanel>
</template>
