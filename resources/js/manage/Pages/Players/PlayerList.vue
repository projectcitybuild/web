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

interface Props {
    success?: string,
    players?: Paginated<Player>,
}
defineProps<Props>()
</script>

<template>
    <Head title="Manage Players" />

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div
                class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div class="w-full md:w-1/2">

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
                    v-slot="source"
                >
                    <PlayerListTable :players="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
</template>
