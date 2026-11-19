<script setup lang="ts">
import { Deferred, Head, useForm } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import AccountListTable from './Partials/AccountListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import { computed, watch, ref } from 'vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import ListPage from '../../Components/ListPage.vue'
import OutlinedButton from '../../Components/OutlinedButton.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'
import { Routes } from '../../../Routes'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Accounts/AccountList']>()

const query = ref({})
const itemCount = ref(0)

const form = useForm({
    username: null,
    email: null,
    activated: null,
    hasPlayer: '',
})

const activeFilterCount = computed(() => [
    Boolean(form.username),
    Boolean(form.email),
    form.activated !== null,
    Boolean(form.hasPlayer),
].filter(Boolean).length)

const { can } = usePermissions()

watch(
    () => form,
    (form) => {
        query.value = {
            ...(form.username ? { username: form.username } : {}),
            ...(form.email ? { email: form.email } : {}),
            ...(form.activated != null ? { activated: form.activated } : {}),
            ...(form.hasPlayer ? { has_player: form.hasPlayer } : {}),
        }
    },
    { deep: true, }
)
</script>

<template>
    <Head title="Manage Accounts" />

    <div class="space-y-6">
        <PageHeader title="Accounts" description="Manage registered website accounts and their access.">
            <template #actions>
                <LinkButton
                    v-if="can('web.manage.accounts.edit')"
                    :href="Routes.manage.accounts.create"
                    variant="primary"
                >
                    <SvgIcon :svg="Icons.plus" aria-hidden="true" />
                    Create Account
                </LinkButton>
            </template>
        </PageHeader>

        <section>
            <SuccessAlert v-if="success" :message="success" class="mb-4"/>

            <ListPage
                :count="accounts ? itemCount : undefined"
                :total="accounts?.total"
                count-label="accounts"
                :filter-count="activeFilterCount"
            >
                <template #filters>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <label for="username" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                            Username
                            <input
                                id="username"
                                v-model="form.username"
                                type="search"
                                class="min-h-10 w-full rounded-md border border-border-strong bg-surface px-3 text-sm text-foreground placeholder:text-subtle focus:border-primary focus:ring-focus"
                                placeholder="Search usernames"
                            >
                        </label>
                        <label for="email" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                            Email
                            <input
                                id="email"
                                v-model="form.email"
                                type="search"
                                class="min-h-10 w-full rounded-md border border-border-strong bg-surface px-3 text-sm text-foreground placeholder:text-subtle focus:border-primary focus:ring-focus"
                                placeholder="Search email addresses"
                            >
                        </label>
                        <label for="activated" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                            Activation status
                            <select
                                id="activated"
                                v-model="form.activated"
                                class="min-h-10 w-full rounded-md border border-border-strong bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-focus"
                            >
                                <option :value="null">Any status</option>
                                <option :value="1">Activated</option>
                                <option :value="0">Not activated</option>
                            </select>
                        </label>
                        <label for="has-player" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                            Has player
                            <select
                                id="has-player"
                                v-model="form.hasPlayer"
                                class="min-h-10 w-full rounded-md border border-border-strong bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-focus"
                            >
                                <option value="">Any</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </label>
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

                <Deferred data="accounts">
                    <template #fallback>
                        <SpinnerRow />
                    </template>

                    <InfinitePagination
                        :initial="accounts"
                        :query="query"
                        v-model:count="itemCount"
                        v-slot="source"
                    >
                        <AccountListTable
                            :accounts="source.data"
                            :class="source.loading ? 'opacity-50' : ''"
                        />
                    </InfinitePagination>
                </Deferred>
            </ListPage>
        </section>
    </div>
</template>
