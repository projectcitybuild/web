<script setup lang="ts">
import { Routes } from '../../../Routes'
import { Deferred, Head, Link } from '@inertiajs/vue3'
import WarningListTable from './Partials/WarningListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import ShowingCount from '../../Components/ShowingCount.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'

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
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <WarningListTable :warnings="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
    </div>
</template>
