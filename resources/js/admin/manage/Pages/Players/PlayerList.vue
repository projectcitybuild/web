<script setup lang="ts">
import { Deferred, Head, Link, useForm } from '@inertiajs/vue3'
import PlayerListTable from './Partials/PlayerListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { computed, ref, watch } from 'vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'
import Input from '../../../shared/Components/Input.vue'
import FilterPanel from '../../Components/FilterPanel.vue'
import ShowingCount from '../../Components/ShowingCount.vue'

defineProps<AdminPageProps<'manage/Players/PlayerList'>>()

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

    <div class="space-y-6">
        <PageHeader title="Players" description="Browse and manage Minecraft players.">
            <template #actions>
                <LinkButton v-if="can('web.manage.players.edit')" href="/manage/players/create" variant="primary">
                    <SvgIcon :svg="Icons.plus" />
                    Create Player
                </LinkButton>
            </template>
        </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card padding="none">
            <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <ShowingCount
                        v-if="players"
                        :from="itemCount > 0 ? 1 : 0"
                        :to="itemCount"
                        :total="players.total"
                        class="text-sm text-muted"
                    />
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
                >
                    <PlayerListTable
                        :players="source.data"
                        :class="source.loading ? 'opacity-50' : ''"
                    />
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
    </div>
</template>
