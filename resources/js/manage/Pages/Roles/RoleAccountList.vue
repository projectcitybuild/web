<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import type { Account } from '../../Data/Account'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import RoleMemberListTable from './Partials/RoleMemberListTable.vue'
import { Role } from '../../Data/Role'
import ToolBar from '../../Components/ToolBar.vue'
import BackButton from '../../Components/BackButton.vue'

interface Props {
    success?: string,
    role: Role,
    accounts: Paginated<Account>,
}

defineProps<Props>()
</script>

<template>
    <Head title="Manage Roles"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ToolBar>
            <template v-slot:left>
                <BackButton href="/manage/roles" />
            </template>
        </ToolBar>

        <Card class="mt-4">
            <InfinitePagination
                :initial="accounts"
                v-slot="source"
            >
                <RoleMemberListTable :accounts="source.data"/>
            </InfinitePagination>
        </Card>
    </section>
</template>
