<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Deferred, Head } from '@inertiajs/vue3'
import WarpListTable from './Partials/WarpListTable.vue'
import InfinitePagination from '../../../../Components/InfinitePagination.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import SpinnerRow from '../../../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../../../Components/SvgIcon.vue'
import usePermissions from '../../../../Composables/usePermissions'
import PageHeader from '../../../../Components/PageHeader.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import ListPage from '../../../../Components/ListPage.vue'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Warps/WarpList']>()

const itemCount = ref(0)

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Warps"/>

    <div class="space-y-6">
    <PageHeader title="Warps" description="Manage teleport locations available to Minecraft players.">
        <template #actions>
            <LinkButton v-if="can('web.manage.warps.edit')" :href="Routes.manage.warps.create" variant="primary">
                <SvgIcon :svg="Icons.plus" />
                Create Warp
            </LinkButton>
        </template>
    </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ListPage
            :count="warps ? itemCount : undefined"
            :total="warps?.total"
            count-label="warps"
        >
            <Deferred data="warps">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="warps"
                    :sort="sort"
                    :direction="direction"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <WarpListTable
                        :warps="source.data"
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
