<script setup lang="ts">
import { Deferred, Head } from '@inertiajs/vue3'
import { Paginated } from '../../../manage/Data/Paginated'
import InfinitePagination from '../../../manage/Components/InfinitePagination.vue'
import SuccessAlert from '../../../manage/Components/SuccessAlert.vue'
import type { BuilderRankApplication } from '../../../manage/Data/BuilderRankApplication'
import BuilderRankApplicationListItems from './Partials/BuilderRankApplicationListItems.vue'
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
        <PageHeader
            title="Builder Rank Applications"
            description="Review builder applications and the applicant's submitted work."
        />

        <SuccessAlert v-if="success" :message="success" />

        <section aria-label="Builder application review queue">
                <Deferred data="applications">
                    <template #fallback>
                        <SpinnerRow />
                    </template>

                    <InfinitePagination
                        :initial="applications"
                        v-slot="source"
                    >
                        <BuilderRankApplicationListItems :applications="source.data"/>
                    </InfinitePagination>
                </Deferred>
        </section>
    </div>
</template>
