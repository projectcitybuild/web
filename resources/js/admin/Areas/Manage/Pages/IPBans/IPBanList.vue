<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Deferred, Head } from '@inertiajs/vue3'
import IPBanListTable from './Partials/IPBanListTable.vue'
import InfinitePagination from '../../../../Components/InfinitePagination.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import SpinnerRow from '../../../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../../../Navigation/Icons'
import usePermissions from '../../../../Composables/usePermissions'
import PageHeader from '../../../../Components/PageHeader.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import ListPage from '../../../../Components/ListPage.vue'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/IPBans/IPBanList']>()

const itemCount = ref(0)

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage IP Bans"/>

    <div class="space-y-6">
    <PageHeader title="IP Bans" description="Manage restrictions on player IP addresses.">
        <template #actions>
            <LinkButton v-if="can('web.manage.ip_bans.edit')" :href="Routes.manage.ipBans.create" variant="primary">
                <SvgIcon :svg="Icons.plus" />
                Create Ban
            </LinkButton>
        </template>
    </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>
        <ListPage
            :count="bans ? itemCount : undefined"
            :total="bans?.total"
            count-label="bans"
        >
            <Deferred data="bans">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="bans"
                    :sort="sort"
                    :direction="direction"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <IPBanListTable
                        :bans="source.data"
                        :sort="source.sort"
                        :direction="source.direction"
                        :on-sort="source.onSort"
                    />
                </InfinitePagination>
            </Deferred>
        </ListPage>
    </section>
    </div>
</template>
