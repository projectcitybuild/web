<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Deferred, Head } from '@inertiajs/vue3'
import PermissionListTable from './Partials/PermissionListTable.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import SpinnerRow from '../../../../Components/SpinnerRow.vue'
import usePermissions from '../../../../Composables/usePermissions'
import { Icons } from '../../../../Navigation/Icons'
import PageHeader from '../../../../Components/PageHeader.vue'
import ListPage from '../../../../Components/ListPage.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import { useServerListSorting } from '../../../../Composables/useTableSorting'

const { can } = usePermissions()

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Permissions/PermissionList']>()
const onSort = useServerListSorting(() => ({ sort: props.sort, direction: props.direction }))
</script>

<template>
    <Head title="Manage Permissions"/>

    <div class="space-y-6">
    <PageHeader title="Permissions" description="Review the permissions available to application roles.">
        <template #actions>
            <LinkButton
                v-if="can('web.manage.permissions.edit')"
                :href="Routes.manage.permissions.create"
                variant="primary"
            >
                <SvgIcon :svg="Icons.plus" aria-hidden="true" />
                Create Permission
            </LinkButton>
        </template>
    </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ListPage :count="props.permissions?.length" count-label="permissions">
            <Deferred data="permissions">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <PermissionListTable
                    :permissions="props.permissions ?? []"
                    :sort="props.sort"
                    :direction="props.direction"
                    :on-sort="onSort"
                />
            </Deferred>
        </ListPage>
    </section>
    </div>
</template>
