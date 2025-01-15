<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import type { Badge } from '../../Data/Badge'
import BadgeListTable from './Partials/BadgeListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { ref } from 'vue'

interface Props {
    success?: string,
    badges?: Paginated<Badge>,
}
defineProps<Props>()

const itemCount = ref(0)
</script>

<template>
    <Head title="Manage Badges"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-row items-center justify-between p-4">
                <div>
                    <span v-if="badges" class="text-sm text-gray-500">
                        Showing <strong>{{ itemCount }}</strong> of <strong>{{ badges?.total ?? 0 }}</strong>
                    </span>
                </div>

                <div>
                    <Link href="/manage/badges/create">
                        <FilledButton variant="primary">
                            <SvgIcon icon="plus" />
                            Create Badge
                        </FilledButton>
                    </Link>
                </div>
            </div>

            <Deferred data="badges">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="badges"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <BadgeListTable :badges="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
</template>
