<script setup lang="ts">
import { Deferred, Head } from '@inertiajs/vue3'
import { Paginated } from '../../../manage/Data/Paginated'
import BanAppealListTable from './Partials/BanAppealListTable.vue'
import InfinitePagination from '../../../manage/Components/InfinitePagination.vue'
import SuccessAlert from '../../../manage/Components/SuccessAlert.vue'
import Card from '../../../manage/Components/Card.vue'
import { BanAppeal } from '../../../manage/Data/BanAppeal'
import SpinnerRow from '../../../manage/Components/SpinnerRow.vue'

interface Props {
    success?: string,
    banAppeals?: Paginated<BanAppeal>,
}
defineProps<Props>()
</script>

<template>
    <Head title="Ban Appeals" />

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
            </div>

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
