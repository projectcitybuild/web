<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import PlayerListTable from './Partials/PlayerListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import type { Player } from '../../Data/Player'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { ref } from 'vue'

interface Props {
    success?: string,
    players?: Paginated<Player>,
}
defineProps<Props>()

const itemCount = ref(0)
</script>

<template>
    <Head title="Manage Players" />

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-row items-center justify-between p-4">
                <div>
                    <span v-if="players" class="text-sm text-gray-500">
                        Showing <strong>{{ itemCount }}</strong> of <strong>{{ players?.total ?? 0 }}</strong>
                    </span>
                </div>

                <div>
                    <Link href="/manage/players/create">
                        <FilledButton variant="primary">
                            <SvgIcon icon="plus" />
                            Create Player
                        </FilledButton>
                    </Link>
                </div>
            </div>

            <Deferred data="players">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="players"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <PlayerListTable :players="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
</template>
