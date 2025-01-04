<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import type { Account } from '../../Data/Account'
import InfinitePagination from '../../Components/InfinitePagination.vue'
import GroupMemberListTable from './Partials/GroupMemberListTable.vue'
import { Group } from '../../Data/Group'
import ToolBar from '../../Components/ToolBar.vue'
import BackButton from '../../Components/BackButton.vue'

interface Props {
    success?: string,
    group: Group,
    accounts: Paginated<Account>,
}

defineProps<Props>()
</script>

<template>
    <Head title="Manage Groups"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ToolBar>
            <template v-slot:left>
                <BackButton href="/manage/groups" />
            </template>
        </ToolBar>

        <div class="mt-4 mx-auto max-w-screen-xl">
            <Card class="overflow-hidden">
                <InfinitePagination
                    :initial="accounts"
                    v-slot="source"
                    class="overflow-x-auto"
                >
                    <GroupMemberListTable :accounts="source.data"/>
                </InfinitePagination>
            </Card>
        </div>
    </section>
</template>
