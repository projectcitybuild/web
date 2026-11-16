<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import type { Account } from '../../Data/Account'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import RoleMemberListTable from './Partials/RoleMemberListTable.vue'
import { Role } from '../../Data/Role'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import ManageBreadcrumbs from '../../Components/ManageBreadcrumbs.vue'

interface Props {
    success?: string,
    role: Role,
    accounts: Paginated<Account>,
}

defineProps<Props>()
</script>

<template>
    <Head :title="'Members of ' + role.name"/>

    <div class="space-y-6">
    <PageHeader :title="'Members of ' + role.name" description="Review the accounts assigned to this role.">
        <template #breadcrumbs>
            <ManageBreadcrumbs :items="[{ label: 'Manage', href: '/manage' }, { label: 'Roles', href: '/manage/roles' }, { label: role.name }, { label: 'Members' }]" />
        </template>
    </PageHeader>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card padding="none" class="mt-4">
            <InfinitePagination
                :initial="accounts"
                v-slot="source"
            >
                <RoleMemberListTable :accounts="source.data"/>
            </InfinitePagination>
        </Card>
    </section>
    </div>
</template>
