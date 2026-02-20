<script setup lang="ts">
import { Deferred, Head, Link, useForm } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import PlayerListTable from './Partials/PlayerListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import type { Player } from '../../Data/Player'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import OutlinedButton from '../../Components/OutlinedButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { ref, watch } from 'vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'

interface Props {
    success?: string,
    players?: Paginated<Player>,
}
defineProps<Props>()

const { can } = usePermissions()

const filterExpanded = ref(false)
const query = ref({})
const itemCount = ref(0)

const form = useForm({
    alias: null,
    uuid: null,
})

watch(
    () => form,
    (form) => {
        query.value = {
            ...(form.alias ? { alias: form.alias } : {}),
            ...(form.uuid ? { uuid: form.uuid } : {}),
        }
    },
    { deep: true, }
)
</script>

<template>
    <Head title="Manage Players" />

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <div class="flex flex-col md:flex-row md:items-start gap-4">
            <Card v-show="filterExpanded" class="p-4 flex flex-col gap-4 md:sticky md:top-24">
                <h2 class="font-bold">Filter</h2>

                <hr />

                <div class="flex flex-col gap-1">
                    <label for="alias" class="text-xs text-gray-700 font-bold">Alias</label>
                    <input
                        v-model="form.alias"
                        type="text"
                        id="alias"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Search..."
                    >
                </div>
                <div class="flex flex-col gap-1">
                    <label for="uuid" class="text-xs text-gray-700 font-bold">UUID</label>
                    <input
                        v-model="form.uuid"
                        type="text"
                        id="uuid"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Search..."
                    >
                </div>

                <hr />

                <OutlinedButton variant="secondary" @click="form.reset()">
                    Clear Filter
                </OutlinedButton>
            </Card>

            <Card class="grow">
                <div class="flex flex-row items-center justify-between p-4">
                    <div>
                        <span v-if="players" class="text-sm text-gray-500">
                            Showing <strong>{{ itemCount }}</strong> of <strong>{{ players?.total ?? 0 }}</strong>
                        </span>
                    </div>

                    <div class="flex gap-2">
                        <OutlinedButton
                            variant="secondary"
                            @click="filterExpanded = !filterExpanded"
                        >
                            <SvgIcon :svg="Icons.filter" />

                            Filter

                            <SvgIcon :svg="filterExpanded ? Icons.chevronUp : Icons.chevronDown" />
                        </OutlinedButton>

                        <Link href="/manage/players/create" v-if="can('web.manage.players.edit')">
                            <FilledButton variant="primary">
                                <SvgIcon :svg="Icons.plus" />
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
                        :query="query"
                        v-model:count="itemCount"
                        v-slot="source"
                        class="border-t border-gray-200"
                    >
                        <PlayerListTable
                            :players="source.data"
                            :class="source.loading ? 'opacity-50' : ''"
                        />
                    </InfinitePagination>
                </Deferred>
            </Card>
        </div>
    </section>
</template>
