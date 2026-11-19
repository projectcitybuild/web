<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Head, Link } from '@inertiajs/vue3'
import ServerTokenListTable from './Partials/ServerTokenListTable.vue'
import Card from '../../../../Components/ManageCard.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import FilledButton from '../../../../Components/FilledButton.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../Navigation/Icons'
import usePermissions from '../../../../Composables/usePermissions'
import PageHeader from '../../../../Components/PageHeader.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import { useServerListSorting } from '../../../../Composables/useTableSorting'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/ServerTokens/ServerTokensList']>()
const onSort = useServerListSorting(() => ({ sort: props.sort, direction: props.direction }))

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Server Tokens"/>

    <div class="space-y-6">
    <PageHeader title="Server Tokens" description="Manage credentials used by trusted applications to access the PCB API.">
        <template #actions>
            <LinkButton v-if="can('web.manage.server_tokens.edit')" :href="Routes.manage.serverTokens.create" variant="primary">
                <SvgIcon :svg="Icons.plus" />
                Create Token
            </LinkButton>
        </template>
    </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>
        <Card padding="none">
            <ServerTokenListTable
                :tokens="props.tokens"
                :sort="props.sort"
                :direction="props.direction"
                :on-sort="onSort"
            />
        </Card>
    </section>
    </div>
</template>
