<script setup lang="ts">
import { Deferred, Head } from '@inertiajs/vue3'
import { Paginated } from '../../../manage/Data/Paginated'
import BanAppealListItems from './Partials/BanAppealListItems.vue'
import InfinitePagination from '../../../manage/Components/InfinitePagination.vue'
import SuccessAlert from '../../../manage/Components/SuccessAlert.vue'
import { BanAppeal } from '../../../manage/Data/BanAppeal'
import SpinnerRow from '../../../manage/Components/SpinnerRow.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'

interface Props {
    success?: string,
    banAppeals?: Paginated<BanAppeal>,
}
defineProps<Props>()
</script>

<template>
    <Head title="Ban Appeals" />

    <div class="space-y-4">
        <PageHeader
            title="Ban Appeals"
            description="Review player requests and follow up on their ban."
        />

        <SuccessAlert v-if="success" :message="success" />

        <section aria-label="Ban appeal review queue">
            <Deferred data="banAppeals">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="banAppeals"
                    v-slot="source"
                >
                    <BanAppealListItems :banAppeals="source.data"/>
                </InfinitePagination>
            </Deferred>
        </section>
    </div>
</template>
