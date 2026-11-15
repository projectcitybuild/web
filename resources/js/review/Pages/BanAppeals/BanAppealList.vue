<script setup lang="ts">
import { Deferred, Head } from '@inertiajs/vue3'
import { Paginated } from '../../../manage/Data/Paginated'
import BanAppealListTable from './Partials/BanAppealListTable.vue'
import InfinitePagination from '../../../manage/Components/InfinitePagination.vue'
import SuccessAlert from '../../../manage/Components/SuccessAlert.vue'
import Card from '../../../shared/Components/Card.vue'
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

    <PageHeader title="Ban Appeals" class="mb-4" />

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card padding="none">
            <Deferred data="banAppeals">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="banAppeals"
                    v-slot="source"
                >
                    <BanAppealListTable :banAppeals="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
</template>
