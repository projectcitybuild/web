<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3'
import type { Role } from '../../Data/Role'
import RoleListTable from './Partials/RoleListTable.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'
import usePermissions from '../../Composables/usePermissions'
import { Icons } from '../../Icons'

interface Props {
    success?: string,
    roles?: Role[],
}

const { can } = usePermissions()

defineProps<Props>()
</script>

<template>
    <Head title="Manage Roles"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-row items-center justify-between p-4">
                <div>
                    <span v-if="roles" class="text-sm text-gray-500">
                        Showing <strong>{{ roles.length }}</strong> of <strong>{{ roles?.length ?? 0 }}</strong>
                    </span>
                </div>
                <div>
                    <Link
                        href="/manage/roles/create"
                        as="button"
                        v-if="can('web.manage.roles.edit')"
                    >
                        <FilledButton variant="primary">
                            <SvgIcon :svg="Icons.plus" />
                            Create Role
                        </FilledButton>
                    </Link>
                </div>
            </div>

            <Deferred data="roles">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <RoleListTable :roles="roles" class="border-t border-gray-200" />
            </Deferred>
        </Card>
    </section>
</template>
