<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'
import { Home } from '../../Data/Home'
import HomeListTable from './Partials/HomeListTable.vue'

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

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-row items-center justify-between p-4">
                <div>
                    <span v-if="homes" class="text-sm text-gray-500">
                        Showing <strong>{{ itemCount }}</strong> of <strong>{{ homes?.total ?? 0 }}</strong>
                    </span>
                </div>

                <div>
                    <Link href="/manage/homes/create" v-if="can('web.manage.homes.edit')">
                        <FilledButton variant="primary">
                            <SvgIcon :svg="Icons.plus" />
                            Create Home
                        </FilledButton>
                    </Link>
                </div>
            </div>

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
        </Card>
    </section>
</template>
