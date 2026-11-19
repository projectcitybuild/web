<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Deferred, Head, Link } from '@inertiajs/vue3'
import WarpListTable from './Partials/WarpListTable.vue'
import InfinitePagination from '../../../../Components/InfinitePagination.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import Card from '../../../../Components/ManageCard.vue'
import FilledButton from '../../../../Components/FilledButton.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import SpinnerRow from '../../../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../../../Navigation/Icons'
import usePermissions from '../../../../Composables/usePermissions'
import ShowingCount from '../../../../Components/ShowingCount.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import LinkButton from '../../../../Components/LinkButton.vue'

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

        <Card padding="none">
            <div class="flex flex-row items-center justify-between py-4">
                <div>
                    <ShowingCount
                        v-if="warps"
                        :from="itemCount > 0 ? 1 : 0"
                        :to="itemCount"
                        :total="warps.total"
                        class="text-sm text-muted"
                    />
                </div>

                <div>
                </div>
            </div>

            <Deferred data="warps">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="warps"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <WarpListTable :warps="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
    </div>
</template>
