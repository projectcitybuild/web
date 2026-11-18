<script setup lang="ts">
import { Deferred, Head } from '@inertiajs/vue3'
import PermissionListTable from './Partials/PermissionListTable.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import usePermissions from '../../Composables/usePermissions'
import { Icons } from '../../Icons'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import ListPage from '../../Components/ListPage.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'

const { can } = usePermissions()

defineProps<AdminPageProps<'manage/Permissions/PermissionList'>>()
</script>

<template>
    <Head title="Manage Permissions"/>

    <div class="space-y-6">
    <PageHeader title="Permissions" description="Review the permissions available to application roles.">
        <template #actions>
            <LinkButton
                v-if="can('web.manage.permissions.edit')"
                href="/manage/permissions/create"
                variant="primary"
            >
                <SvgIcon :svg="Icons.plus" aria-hidden="true" />
                Create Permission
            </LinkButton>
        </template>
    </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ListPage :count="permissions?.length" count-label="permissions">
            <Deferred data="permissions">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <PermissionListTable :permissions="permissions ?? []" />
            </Deferred>
        </ListPage>
    </section>
    </div>
</template>
