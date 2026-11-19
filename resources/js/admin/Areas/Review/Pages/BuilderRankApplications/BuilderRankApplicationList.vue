<script setup lang="ts">
import { Deferred, Head } from '@inertiajs/vue3'
import InfinitePagination from '../../../../Components/InfinitePagination.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import BuilderRankApplicationListItems from './Partials/BuilderRankApplicationListItems.vue'
import SpinnerRow from '../../../../Components/SpinnerRow.vue'
import PageHeader from '../../../../Components/PageHeader.vue'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['review/BuilderRankApplications/BuilderRankApplicationList']>()
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
