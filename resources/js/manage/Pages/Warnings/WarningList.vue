<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import type { PlayerWarning } from '../../Data/PlayerWarning'
import WarningListTable from './Partials/WarningListTable.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import { ref } from 'vue'
import { Icons } from '../../Icons'
import usePermissions from '../../Composables/usePermissions'

interface Props {
    success?: string,
    warnings?: Paginated<PlayerWarning>,
}
defineProps<Props>()

const itemCount = ref(0)

const { can } = usePermissions()
</script>

<template>
    <Head title="Manage Warnings"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-row items-center justify-between p-4">
                <div>
                    <span v-if="warnings" class="text-sm text-gray-500">
                        Showing <strong>{{ itemCount }}</strong> of <strong>{{ warnings?.total ?? 0 }}</strong>
                    </span>
                </div>
                <div>
                    <Link href="/manage/warnings/create" v-if="can('web.manage.warnings.edit')">
                        <FilledButton variant="primary">
                            <SvgIcon :svg="Icons.plus" />
                            Create Warning
                        </FilledButton>
                    </Link>
                </div>
            </div>

            <Deferred data="warnings">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <InfinitePagination
                    :initial="warnings"
                    v-model:count="itemCount"
                    v-slot="source"
                >
                    <WarningListTable :warnings="source.data"/>
                </InfinitePagination>
            </Deferred>
        </Card>
    </section>
</template>
