<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Deferred, Head } from '@inertiajs/vue3'
import RoleListTable from './Partials/RoleListTable.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import SpinnerRow from '../../../../Components/SpinnerRow.vue'
import usePermissions from '../../../../Composables/usePermissions'
import { Icons } from '../../../../Navigation/Icons'
import PageHeader from '../../../../Components/PageHeader.vue'
import ListPage from '../../../../Components/ListPage.vue'
import LinkButton from '../../../../Components/LinkButton.vue'

const { can } = usePermissions()

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Roles/RoleList']>()
</script>

<template>
    <Head title="Manage Roles"/>

    <div class="space-y-6">
    <PageHeader title="Roles" description="Manage roles and the access granted to their members.">
        <template #actions>
            <LinkButton
                v-if="can('web.manage.roles.edit')"
                :href="Routes.manage.roles.create"
                variant="primary"
            >
                <SvgIcon :svg="Icons.plus" aria-hidden="true" />
                Create Role
            </LinkButton>
        </template>
    </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ListPage :count="roles?.length" count-label="roles">
            <Deferred data="roles">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <RoleListTable :roles="roles ?? []" />
            </Deferred>
        </ListPage>
    </section>
    </div>
</template>
