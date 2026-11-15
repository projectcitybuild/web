<script setup lang="ts">
import { Deferred, Head, Link, useForm } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import AccountListTable from './Partials/AccountListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import type { Account } from '../../Data/Account'
import { computed, watch, ref } from 'vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import FilterPanel from '../../Components/FilterPanel.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'

interface Props {
    success?: string,
    accounts?: Paginated<Account>,
}
const props = defineProps<Props>()

const query = ref({})
const itemCount = ref(0)

const form = useForm({
    username: null,
    email: null,
    activated: null,
})

const activeFilterCount = computed(() => [
    Boolean(form.username),
    Boolean(form.email),
    form.activated !== null,
].filter(Boolean).length)

const { can } = usePermissions()

watch(
    () => form,
    (form) => {
        query.value = {
            ...(form.username ? { username: form.username } : {}),
            ...(form.email ? { email: form.email } : {}),
            ...(form.activated != null ? { activated: form.activated } : {}),
        }
    },
    { deep: true, }
)
</script>

<template>
    <Head title="Manage Accounts" />

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-wrap items-center justify-between gap-3 p-4">
                <div>
                    <span v-if="accounts" class="text-sm text-gray-500">
                        Showing <strong>{{ itemCount }}</strong> of <strong>{{ accounts?.total ?? 0 }}</strong>
                    </span>
                </div>

                <div class="flex flex-wrap gap-2">
                    <FilterPanel
                        id="account-filters"
                        :active-count="activeFilterCount"
                        apply-label="Done"
                        @clear="form.reset()"
                    >
                        <div class="grid gap-4 sm:grid-cols-2">
                            <label for="username" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                                Username
                                <input
                                    id="username"
                                    v-model="form.username"
                                    type="search"
                                    class="min-h-11 w-full rounded-lg border border-border bg-surface px-3 text-sm text-foreground placeholder:text-muted focus:border-primary focus:ring-primary"
                                    placeholder="Search usernames"
                                >
                            </label>
                            <label for="email" class="flex flex-col gap-1.5 text-sm font-medium text-foreground">
                                Email
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="search"
                                    class="min-h-11 w-full rounded-lg border border-border bg-surface px-3 text-sm text-foreground placeholder:text-muted focus:border-primary focus:ring-primary"
                                    placeholder="Search email addresses"
                                >
                            </label>
                            <label for="activated" class="flex flex-col gap-1.5 text-sm font-medium text-foreground sm:col-span-2">
                                Activation status
                                <select
                                    id="activated"
                                    v-model="form.activated"
                                    class="min-h-11 w-full rounded-lg border border-border bg-surface px-3 text-sm text-foreground focus:border-primary focus:ring-primary"
                                >
                                    <option :value="null">Any status</option>
                                    <option :value="1">Activated</option>
                                    <option :value="0">Not activated</option>
                                </select>
                            </label>
                        </div>
                    </FilterPanel>

                    <Link
                        href="/manage/accounts/create"
                        as="button"
                        v-if="can('web.manage.accounts.edit')"
                    >
                        <FilledButton variant="primary">
                            <SvgIcon :svg="Icons.plus" />
                            Create Account
                        </FilledButton>
                    </Link>
                </div>
            </div>

            <Deferred data="accounts">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="accounts"
                    :query="query"
                    v-model:count="itemCount"
                    v-slot="source"
                    class="border-t border-gray-200"
                >
                    <AccountListTable
                        :accounts="source.data"
                        :class="source.loading ? 'opacity-50' : ''"
                    />
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
</template>
