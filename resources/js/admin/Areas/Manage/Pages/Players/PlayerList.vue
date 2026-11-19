<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Deferred, Head, useForm } from '@inertiajs/vue3'
import PlayerListTable from './Partials/PlayerListTable.vue'
import InfinitePagination from '../../../../Components/InfinitePagination.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import SpinnerRow from '../../../../Components/SpinnerRow.vue'
import { computed, ref, watch } from 'vue'
import { Icons } from '../../../../Navigation/Icons'
import usePermissions from '../../../../Composables/usePermissions'
import PageHeader from '../../../../Components/PageHeader.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import Input from '../../../../Components/Input.vue'
import ListPage from '../../../../Components/ListPage.vue'
import OutlinedButton from '../../../../Components/OutlinedButton.vue'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Players/PlayerList']>()

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
                <LinkButton v-if="can('web.manage.players.edit')" :href="Routes.manage.players.create" variant="primary">
                    <SvgIcon :svg="Icons.plus" />
                    Create Player
                </LinkButton>
            </template>
        </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ListPage
            :count="players ? itemCount : undefined"
            :total="players?.total"
            count-label="players"
            :filter-count="activeFilterCount"
        >
            <template #filters>
                <div class="grid gap-4 sm:grid-cols-2">
                    <Input v-model="form.alias" id="alias" label="Alias" placeholder="Search aliases" />
                    <Input v-model="form.uuid" id="uuid" label="UUID" placeholder="Search UUIDs" />
                </div>
                <div class="mt-4 flex justify-end">
                    <OutlinedButton
                        variant="secondary"
                        :disabled="activeFilterCount === 0"
                        @click="form.reset()"
                    >
                        Clear filters
                    </OutlinedButton>
                </div>
            </template>

            <Deferred data="players">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="players"
                    :query="query"
                    :sort="sort"
                    :direction="direction"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <PlayerListTable
                        :players="source.data"
                        :sort="source.sort"
                        :direction="source.direction"
                        :on-sort="source.onSort"
                        :class="source.loading ? 'opacity-50' : ''"
                    />
                </InfinitePagination>
            </Deferred>
        </ListPage>
    </section>
    </div>
</template>
