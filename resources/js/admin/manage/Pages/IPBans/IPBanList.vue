<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3'
import IPBanListTable from './Partials/IPBanListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import Card from '../../Components/Card.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import ShowingCount from '../../Components/ShowingCount.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/IPBans/IPBanList']>()

const itemCount = ref(0)

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage IP Bans"/>

    <div class="space-y-6">
    <PageHeader title="IP Bans" description="Manage restrictions on Minecraft server IP addresses.">
        <template #actions>
            <LinkButton v-if="can('web.manage.ip_bans.edit')" href="/manage/ip-bans/create" variant="primary">
                <SvgIcon :svg="Icons.plus" />
                Create Ban
            </LinkButton>
        </template>
    </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>
        <Card padding="none">
            <div class="flex flex-row items-center justify-between py-4">
                <div>
                    <ShowingCount
                        v-if="bans"
                        :from="itemCount > 0 ? 1 : 0"
                        :to="itemCount"
                        :total="bans.total"
                        class="text-sm text-muted"
                    />
                </div>
                <div>
                </div>
            </div>

            <Deferred data="bans">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="bans"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <IPBanListTable :bans="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
    </div>
</template>
