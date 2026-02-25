<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3'
import PermissionListTable from './Partials/PermissionListTable.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import usePermissions from '../../Composables/usePermissions'
import { Icons } from '../../Icons'
import { Permission } from '../../Data/Permission'

interface Props {
    success?: string,
    permissions: Permission[],
}

const { can } = usePermissions()

defineProps<Props>()
</script>

<template>
    <Head title="Manage Permissions"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-row items-center justify-between p-4">
                <div></div>
                <div>
                    <Link
                        href="/manage/permissions/create"
                        as="button"
                        v-if="can('web.manage.permissions.edit')"
                    >
                        <FilledButton variant="primary">
                            <SvgIcon :svg="Icons.plus" />
                            Create Permission
                        </FilledButton>
                    </Link>
                </div>
            </div>

            <Deferred data="permissions">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <PermissionListTable :permissions="permissions" class="border-t border-gray-200" />
            </Deferred>
        </Card>
    </section>
</template>
