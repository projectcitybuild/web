<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Deferred, Head } from '@inertiajs/vue3'
import InfinitePagination from '../../../../Components/InfinitePagination.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import SpinnerRow from '../../../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../../../Components/SvgIcon.vue'
import usePermissions from '../../../../Composables/usePermissions'
import HomeListTable from './Partials/HomeListTable.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import ListPage from '../../../../Components/ListPage.vue'
import LinkButton from '../../../../Components/LinkButton.vue'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Homes/HomeList']>()

const itemCount = ref(0)

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Homes"/>

    <div class="space-y-6">
        <PageHeader title="Homes" description="Manage saved player locations.">
            <template #actions>
                <LinkButton
                    v-if="can('web.manage.homes.edit')"
                    :href="Routes.manage.homes.create"
                    variant="primary"
                >
                    <SvgIcon :svg="Icons.plus" aria-hidden="true" />
                    Create Home
                </LinkButton>
            </template>
        </PageHeader>

        <section>
            <SuccessAlert v-if="success" :message="success" class="mb-4"/>

            <ListPage
                :count="homes ? itemCount : undefined"
                :total="homes?.total"
                count-label="homes"
            >
                <Deferred data="homes">
                    <template #fallback>
                        <SpinnerRow />
                    </template>

                    <InfinitePagination
                        :initial="homes"
                        :sort="sort"
                        :direction="direction"
                        v-model:count="itemCount"
                        v-slot="source"
                    >
                        <HomeListTable
                            :homes="source.data"
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
