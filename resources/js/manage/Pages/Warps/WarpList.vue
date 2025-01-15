<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import type { Warp } from '../../Data/Warp'
import WarpListTable from './Partials/WarpListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'

interface Props {
    success?: string,
    warps?: Paginated<Warp>,
}

defineProps<Props>()
</script>

<template>
    <Head title="Manage Warps"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div
                class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">

                </div>
                <div>
                    <Link href="/manage/minecraft/warps/create">
                        <FilledButton variant="primary">
                            <SvgIcon icon="plus" />
                            Create Warp
                        </FilledButton>
                    </Link>
                </div>
            </div>

            <Deferred data="warps">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="warps"
                    v-slot="source"
                >
                    <WarpListTable :warps="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
</template>
