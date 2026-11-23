<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Deferred, Head } from '@inertiajs/vue3'
import BadgeListTable from './Partials/BadgeListTable.vue'
import InfinitePagination from '../../../../Components/InfinitePagination.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import SpinnerRow from '../../../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../../../Components/SvgIcon.vue'
import usePermissions from '../../../../Composables/usePermissions'
import PageHeader from '../../../../Components/PageHeader.vue'
import ListPage from '../../../../Components/ListPage.vue'
import LinkButton from '../../../../Components/LinkButton.vue'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Badges/BadgeList']>()

const itemCount = ref(0)

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Badges"/>

    <div class="space-y-6">
        <PageHeader title="Badges" description="Manage player badges and achievements.">
            <template #actions>
                <LinkButton
                    v-if="can('web.manage.badges.edit')"
                    :href="Routes.manage.badges.create"
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
                    :sort="sort"
                    :direction="direction"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <BadgeListTable
                        :badges="source.data"
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
