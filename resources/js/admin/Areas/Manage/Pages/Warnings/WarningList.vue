<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Deferred, Head, Link } from '@inertiajs/vue3'
import WarningListTable from './Partials/WarningListTable.vue'
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

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Warnings/WarningList']>()

const itemCount = ref(0)

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Warnings"/>

    <div class="space-y-6">
    <PageHeader title="Warnings" description="Review player warnings and recorded infractions.">
        <template #actions>
            <LinkButton v-if="can('web.manage.warnings.edit')" :href="Routes.manage.warnings.create" variant="primary">
                <SvgIcon :svg="Icons.plus" />
                Create Warning
            </LinkButton>
        </template>
    </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card padding="none">
            <div class="flex flex-row items-center justify-between py-4">
                <div>
                    <ShowingCount
                        v-if="warnings"
                        :from="itemCount > 0 ? 1 : 0"
                        :to="itemCount"
                        :total="warnings.total"
                        class="text-sm text-muted"
                    />
                </div>
                <div>
                </div>
            </div>

            <Deferred data="warnings">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="warnings"
                    :sort="sort"
                    :direction="direction"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <WarningListTable
                        :warnings="source.data"
                        :sort="source.sort"
                        :direction="source.direction"
                        :on-sort="source.onSort"
                    />
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
    </div>
</template>
