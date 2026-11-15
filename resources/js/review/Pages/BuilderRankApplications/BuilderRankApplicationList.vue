<script setup lang="ts">
import { Deferred, Head } from '@inertiajs/vue3'
import { Paginated } from '../../../manage/Data/Paginated'
import InfinitePagination from '../../../manage/Components/InfinitePagination.vue'
import SuccessAlert from '../../../manage/Components/SuccessAlert.vue'
import Card from '../../../shared/Components/Card.vue'
import type { BuilderRankApplication } from '../../../manage/Data/BuilderRankApplication'
import BuilderRankApplicationListTable from './Partials/BuilderRankApplicationListTable.vue'
import SpinnerRow from '../../../manage/Components/SpinnerRow.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'

interface Props {
    success?: string,
    applications?: Paginated<BuilderRankApplication>,
}
defineProps<Props>()
</script>

<template>
    <Head title="Builder Rank Applications" />

    <div class="space-y-4">
        <PageHeader title="Builder Rank Applications" />

        <section>
            <SuccessAlert v-if="success" :message="success" class="mb-4"/>

            <Card padding="none">
                <Deferred data="applications">
                    <template #fallback>
                        <SpinnerRow />
                    </template>

                    <InfinitePagination
                        :initial="applications"
                        v-slot="source"
                    >
                        <BuilderRankApplicationListTable :applications="source.data"/>
                    </InfinitePagination>
                </Deferred>
            </Card>
        </section>
    </div>
</template>
