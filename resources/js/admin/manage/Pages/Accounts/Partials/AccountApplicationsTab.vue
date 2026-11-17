<script setup lang="ts">
import type { Account } from '../../../Data/Account'
import Card from '../../../Components/Card.vue'
import AccountBanAppealsTable from './AccountBanAppealsTable.vue'
import AccountBuilderRankApplicationsTable from './AccountBuilderRankApplicationsTable.vue'
import TabPanel from '../../../Components/TabPanel.vue'

interface Props {
    account: Account,
    activeTab: string,
}
const props = defineProps<Props>()
</script>

<template>
    <TabPanel tab-id="applications" id-prefix="account" :active-tab="props.activeTab" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
        <Card padding="none">
            <div class="border-b border-border p-4">
                <h2 class="text-base font-semibold text-foreground">Ban appeals</h2>
            </div>
            <AccountBanAppealsTable :account_id="props.account.id" />
        </Card>

        <Card padding="none">
            <div class="border-b border-border p-4">
                <h2 class="text-base font-semibold text-foreground">Builder applications</h2>
            </div>
            <AccountBuilderRankApplicationsTable :account_id="props.account.id" />
        </Card>
    </TabPanel>
</template>
