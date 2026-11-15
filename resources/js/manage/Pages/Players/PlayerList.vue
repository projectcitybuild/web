<script setup lang="ts">
import { Deferred, Head, Link, useForm } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import PlayerListTable from './Partials/PlayerListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import type { Player } from '../../Data/Player'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { computed, ref, watch } from 'vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import Input from '../../../shared/Components/Input.vue'
import FilterPanel from '../../Components/FilterPanel.vue'

interface Props {
    success?: string,
    players?: Paginated<Player>,
}
defineProps<Props>()

const { can } = usePermissions()

const query = ref({})
const itemCount = ref(0)

const form = useForm({
    alias: null,
    uuid: null,
})

const activeFilterCount = computed(() => [Boolean(form.alias), Boolean(form.uuid)].filter(Boolean).length)

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

    <PageHeader title="Players" class="mb-4" />

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span v-if="players" class="text-sm text-muted">
                        Showing <strong>{{ itemCount }}</strong> of <strong>{{ players?.total ?? 0 }}</strong>
                    </span>
                </div>

                <div class="flex flex-wrap gap-2 sm:justify-end">
                    <FilterPanel
                        id="player-filters"
                        :active-count="activeFilterCount"
                        apply-label="Done"
                        @clear="form.reset()"
                    >
                        <div class="grid gap-4">
                            <Input v-model="form.alias" id="alias" label="Alias" placeholder="Search aliases" />
                            <Input v-model="form.uuid" id="uuid" label="UUID" placeholder="Search UUIDs" />
                        </div>
                    </FilterPanel>

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
                    class="border-t border-border"
                >
                    <PlayerListTable
                        :players="source.data"
                        :class="source.loading ? 'opacity-50' : ''"
                    />
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
</template>
