<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import RoleMemberListTable from './Partials/RoleMemberListTable.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Roles/RoleAccountList']>()
</script>

<template>
    <Head :title="'Members of ' + role.name"/>

    <div class="space-y-6">
    <PageHeader :title="'Members of ' + role.name" description="Review the accounts assigned to this role.">
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
