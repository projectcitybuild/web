<script setup lang="ts">
import { Routes } from '../../../../../Navigation/Routes'
import Card from '../../../../../Components/ManageCard.vue'
import usePermissions from '../../../../../Composables/usePermissions'
import AccountDonationsTable from './AccountDonationsTable.vue'
import TabPanel from '../../../../../Components/TabPanel.vue'
import LinkButton from '../../../../../Components/LinkButton.vue'

interface Props {
    account: App.Http.Data.AdminAccountData,
    activeTab: string,
}
const props = defineProps<Props>()

const { can } = usePermissions()
</script>

<template>
    <TabPanel tab-id="contributions" id-prefix="account" :active-tab="props.activeTab" class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-2">
        <Card v-if="can('web.manage.donations.view')" padding="none" class="xl:col-span-2">
            <div class="flex items-center justify-between gap-4 border-b border-border p-4">
                <h2 class="text-base font-semibold text-foreground">Donations</h2>
                <LinkButton :href="Routes.manage.accounts.badges(props.account.id)" variant="secondary">Create</LinkButton>
            </div>
            <div class="overflow-x-auto">
                <AccountDonationsTable :donations="props.account.donations ?? []" />
            </div>
        </Card>
    </TabPanel>
</template>
