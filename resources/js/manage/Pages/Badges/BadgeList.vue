<script setup lang="ts">
import { Deferred, Head } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import type { Badge } from '../../Data/Badge'
import BadgeListTable from './Partials/BadgeListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import ManageBreadcrumbs from '../../Components/ManageBreadcrumbs.vue'
import ListPage from '../../Components/ListPage.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'

interface Props {
    success?: string,
    badges?: Paginated<Badge>,
}
defineProps<Props>()

const itemCount = ref(0)

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Badges"/>

    <div class="space-y-6">
        <PageHeader title="Badges" description="Manage player badges and achievements.">
            <template #breadcrumbs>
                <ManageBreadcrumbs :items="[{ label: 'Manage', href: '/manage' }, { label: 'Badges' }]" />
            </template>
            <template #actions>
                <LinkButton
                    v-if="can('web.manage.badges.edit')"
                    href="/manage/badges/create"
                    variant="primary"
                >
                    <SvgIcon :svg="Icons.plus" aria-hidden="true" />
                    Create Badge
                </LinkButton>
            </template>
        </PageHeader>

        <section>
            <SuccessAlert v-if="success" :message="success" class="mb-4"/>

            <ListPage
                :count="badges ? itemCount : undefined"
                :total="badges?.total"
                count-label="badges"
            >
            <Deferred data="badges">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="badges"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <BadgeListTable :badges="source.data"/>
                </InfinitePagination>
            </Deferred>
            </ListPage>
        </section>
    </div>
</template>
