<script setup lang="ts">
import { Deferred, Head } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import { Home } from '../../Data/Home'
import HomeListTable from './Partials/HomeListTable.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import ListPage from '../../Components/ListPage.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'

interface Props {
    success?: string,
    homes?: Paginated<Home>,
}
defineProps<Props>()

const itemCount = ref(0)

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Homes"/>

    <div class="space-y-6">
        <PageHeader title="Homes" description="Manage saved player locations.">
            <template #actions>
                <LinkButton
                    v-if="can('web.manage.homes.edit')"
                    href="/manage/homes/create"
                    variant="primary"
                >
                    <SvgIcon :svg="Icons.plus" aria-hidden="true" />
                    Create Home
                </LinkButton>
            </template>
        </PageHeader>

        <section>
            <SuccessAlert v-if="success" :message="success" class="mb-4"/>

            <ListPage
                :count="homes ? itemCount : undefined"
                :total="homes?.total"
                count-label="homes"
            >
                <Deferred data="homes">
                    <template #fallback>
                        <SpinnerRow />
                    </template>

                    <InfinitePagination
                        :initial="homes"
                        v-model:count="itemCount"
                        v-slot="source"
                    >
                        <HomeListTable :homes="source.data"/>
                    </InfinitePagination>
                </Deferred>
            </ListPage>
        </section>
    </div>
</template>
