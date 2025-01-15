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
import { ref } from 'vue'

interface Props {
    success?: string,
    warps?: Paginated<Warp>,
}
defineProps<Props>()

const itemCount = ref(0)
</script>

<template>
    <Head title="Manage Warps"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-row items-center justify-between p-4">
                <div>
                    <span v-if="warps" class="text-sm text-gray-500">
                        Showing <strong>{{ itemCount }}</strong> of <strong>{{ warps?.total ?? 0 }}</strong>
                    </span>
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
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <WarpListTable :warps="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
</template>
