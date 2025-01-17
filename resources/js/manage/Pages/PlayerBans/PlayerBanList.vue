<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3'
import BanListTable from './Partials/PlayerBanListTable.vue'
import { Paginated } from '../../Data/Paginated'
import { PlayerBan } from '../../Data/PlayerBan'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import Card from '../../Components/Card.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { ref } from 'vue'

interface Props {
    bans?: Paginated<PlayerBan>,
    success?: string,
}
defineProps<Props>()

const itemCount = ref(0)
</script>

<template>
    <Head title="Manage Bans"/>

    <SuccessAlert v-if="success" :message="success" class="mb-4"/>

    <section>
        <Card>
            <div class="flex flex-row items-center justify-between p-4">
                <div>
                    <span v-if="bans" class="text-sm text-gray-500">
                        Showing <strong>{{ itemCount }}</strong> of <strong>{{ bans?.total ?? 0 }}</strong>
                    </span>
                </div>
                <div>
                    <Link href="/manage/player-bans/create">
                        <FilledButton variant="primary">
                            <SvgIcon icon="plus" />
                            Create Ban
                        </FilledButton>
                    </Link>
                </div>
            </div>

            <Deferred data="bans">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="bans"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <BanListTable :bans="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
</template>
