<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Head } from '@inertiajs/vue3'
import ServerListTable from './Partials/ServerListTable.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../Navigation/Icons'
import usePermissions from '../../../../Composables/usePermissions'
import PageHeader from '../../../../Components/PageHeader.vue'
import ListPage from '../../../../Components/ListPage.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import { useServerListSorting } from '../../../../Composables/useTableSorting'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Servers/ServerList']>()
const onSort = useServerListSorting(() => ({ sort: props.sort, direction: props.direction }))

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Servers"/>

    <div class="space-y-6">
    <PageHeader title="Servers" description="Manage registered Minecraft servers and their integrations.">
        <template #actions>
            <LinkButton
                v-if="can('web.manage.servers.edit')"
                :href="Routes.manage.servers.create"
                variant="primary"
            >
                <SvgIcon :svg="Icons.plus" aria-hidden="true" />
                Create Server
            </LinkButton>
        </template>
    </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>
        <ListPage :count="props.servers.length" count-label="servers">
            <ServerListTable
                :servers="props.servers"
                :sort="props.sort"
                :direction="props.direction"
                :on-sort="onSort"
            />
        </ListPage>
    </section>
    </div>
</template>
