<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Deferred, Head, useForm } from '@inertiajs/vue3'
import DonationPerkListTable from './Partials/DonationPerkListTable.vue'
import InfinitePagination from '../../../../Components/InfinitePagination.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import SpinnerRow from '../../../../Components/SpinnerRow.vue'
import { computed, ref, watch } from 'vue'
import { Icons } from '../../../../Components/SvgIcon.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import Input from '../../../../Components/Input.vue'
import Select from '../../../../Components/Select.vue'
import OutlinedButton from '../../../../Components/OutlinedButton.vue'
import ListPage from '../../../../Components/ListPage.vue'
import usePermissions from '../../../../Composables/usePermissions'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/DonationPerks/DonationPerkList']>()

const itemCount = ref(0)
const query = ref({})
const form = useForm({
    search: '',
    status: '',
})

const activeFilterCount = computed(() => [
    Boolean(form.search),
    Boolean(form.status),
].filter(Boolean).length)

watch(
    () => form,
    (form) => {
        query.value = {
            ...(form.search ? { search: form.search } : {}),
            ...(form.status ? { status: form.status } : {}),
        }
    },
    { deep: true },
)

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Donation Perks" />

    <div class="space-y-6">
        <PageHeader title="Donations Perks" description="Manage account donation benefits.">
            <template #actions>
                <LinkButton
                    v-if="can('web.manage.donations.edit')"
                    :href="Routes.manage.donationPerks.create"
                    variant="primary"
                >
                    <SvgIcon :svg="Icons.plus" aria-hidden="true" />
                    Create Perk
                </LinkButton>
            </template>
        </PageHeader>

        <section>
            <SuccessAlert v-if="success" :message="success" class="mb-4" />

            <ListPage
                :count="perks ? itemCount : undefined"
                :total="perks?.total"
                count-label="perks"
                :filter-count="activeFilterCount"
            >
                <template #filters>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <Input
                            v-model.trim="form.search"
                            type="search"
                            label="Search perks"
                            placeholder="Account, tier, or ID"
                        />
                        <Select v-model="form.status" label="Status">
                            <option value="">All statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive or expired</option>
                        </Select>
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

                <Deferred data="perks">
                    <template #fallback>
                        <SpinnerRow />
                    </template>

                    <InfinitePagination
                        :initial="perks"
                        :path="Routes.manage.donationPerks.list"
                        :query="query"
                        :sort="sort"
                        :direction="direction"
                        v-model:count="itemCount"
                        v-slot="source"
                    >
                        <DonationPerkListTable
                            :perks="source.data"
                            :sort="source.sort"
                            :direction="source.direction"
                            :on-sort="source.onSort"
                        />
                    </InfinitePagination>
                </Deferred>
            </ListPage>
        </section>
    </div>
</template>
