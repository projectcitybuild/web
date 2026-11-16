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
import FilterPanel from '../../Components/FilterPanel.vue'
import PlayerPicker from '../../Components/PlayerPicker.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { computed, ref, watch } from 'vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import ShowingCount from '../../Components/ShowingCount.vue'

interface Props {
    bans?: Paginated<PlayerBan>,
    success?: string,
}
defineProps<Props>()

const { can } = usePermissions()

const itemCount = ref(0)
const player = ref('')
const reason = ref('')
const status = ref('')
const hasExpiry = ref('')
const createdAt = ref('')
const expiresAt = ref('')
const bannerUuid = ref<string | null>(null)
const bannerAlias = ref<string | null>(null)
const query = ref({})

const activeFilterCount = computed(() => [
    Boolean(player.value),
    Boolean(reason.value),
    Boolean(status.value),
    Boolean(hasExpiry.value),
    Boolean(createdAt.value),
    Boolean(expiresAt.value),
    Boolean(bannerUuid.value),
].filter(Boolean).length)

watch([player, reason, status, hasExpiry, createdAt, expiresAt, bannerUuid], () => {
    query.value = {
        ...(player.value ? { player: player.value } : {}),
        ...(reason.value ? { reason: reason.value } : {}),
        ...(status.value ? { status: status.value } : {}),
        ...(hasExpiry.value ? { has_expiry: hasExpiry.value } : {}),
        ...(createdAt.value ? { created_at: createdAt.value } : {}),
        ...(expiresAt.value ? { expires_at: expiresAt.value } : {}),
        ...(bannerUuid.value ? { banner_uuid: bannerUuid.value } : {}),
    }
})

function clearFilters() {
    player.value = ''
    reason.value = ''
    status.value = ''
    hasExpiry.value = ''
    createdAt.value = ''
    expiresAt.value = ''
    bannerUuid.value = null
    bannerAlias.value = null
}
</script>

<template>
    <Head title="Manage Bans"/>

    <SuccessAlert v-if="success" :message="success" class="mb-4"/>

    <section>
        <Card>
            <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <ShowingCount
                        v-if="bans"
                        :from="itemCount > 0 ? 1 : 0"
                        :to="itemCount"
                        :total="bans.total"
                        class="text-sm text-gray-500"
                    />
                </div>
                <div class="flex flex-wrap gap-2 sm:justify-end">
                    <FilterPanel
                        id="player-ban-filters"
                        :active-count="activeFilterCount"
                        apply-label="Done"
                        @clear="clearFilters"
                    >
                        <div class="grid gap-4">
                            <div class="flex flex-col gap-1.5">
                                <label for="name" class="text-sm font-medium text-foreground">Banned by</label>
                                <PlayerPicker
                                    v-model:uuid="bannerUuid"
                                    v-model:alias="bannerAlias"
                                />
                            </div>
                            <label for="ban-player" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                                Player
                                <input
                                    id="ban-player"
                                    v-model="player"
                                    type="search"
                                    class="min-h-11 w-full rounded-lg border border-border bg-surface px-3 text-sm text-foreground placeholder:text-muted focus:border-primary focus:ring-primary"
                                    placeholder="Player name or UUID"
                                >
                            </label>
                            <label for="ban-reason" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                                Reason
                                <input
                                    id="ban-reason"
                                    v-model="reason"
                                    type="search"
                                    class="min-h-11 w-full rounded-lg border border-border bg-surface px-3 text-sm text-foreground placeholder:text-muted focus:border-primary focus:ring-primary"
                                    placeholder="Search ban reasons"
                                >
                            </label>
                            <label for="ban-status" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                                Status
                                <select
                                    id="ban-status"
                                    v-model="status"
                                    class="min-h-11 w-full rounded-lg border border-border bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-primary"
                                >
                                    <option value="">Any status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </label>
                            <label for="ban-has-expiry" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                                Has expiry
                                <select
                                    id="ban-has-expiry"
                                    v-model="hasExpiry"
                                    class="min-h-11 w-full rounded-lg border border-border bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-primary"
                                >
                                    <option value="">Any</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </label>
                            <label for="ban-expires-at" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                                Expiry date
                                <input
                                    id="ban-expires-at"
                                    v-model="expiresAt"
                                    type="date"
                                    class="min-h-11 w-full rounded-lg border border-border bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-primary"
                                >
                            </label>
                            <label for="ban-created-at" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                                Created date
                                <input
                                    id="ban-created-at"
                                    v-model="createdAt"
                                    type="date"
                                    class="min-h-11 w-full rounded-lg border border-border bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-primary"
                                >
                            </label>
                        </div>
                    </FilterPanel>

                    <Link href="/manage/player-bans/create" v-if="can('web.manage.uuid_bans.edit')">
                        <FilledButton variant="primary">
                            <SvgIcon :svg="Icons.plus" />
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
                    :query="query"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <BanListTable :bans="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
</template>
