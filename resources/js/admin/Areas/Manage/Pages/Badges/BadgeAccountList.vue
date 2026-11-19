<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { Routes } from '../../../../Navigation/Routes'
import InfinitePagination from '../../../../Components/InfinitePagination.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import Card from '../../../../Components/ManageCard.vue'
import LinkButton from '../../../../Components/LinkButton.vue'
import RoleMemberListTable from '../Roles/Partials/RoleMemberListTable.vue'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Badges/BadgeAccountList']>()
</script>

<template>
    <Head :title="'Accounts with ' + props.badge.display_name" />

    <div class="space-y-6">
        <PageHeader
            :title="'Accounts with ' + props.badge.display_name"
            description="Review the accounts assigned this badge."
        >
            <template #actions>
                <LinkButton :href="Routes.manage.badges.list" variant="outline">
                    Back to Badges
                </LinkButton>
            </template>
        </PageHeader>

        <section>
            <Card padding="none">
                <InfinitePagination
                    :initial="props.accounts"
                    :sort="props.sort"
                    :direction="props.direction"
                    v-slot="source"
                >
                    <RoleMemberListTable
                        :accounts="source.data"
                        :sort="source.sort"
                        :direction="source.direction"
                        :on-sort="source.onSort"
                    />
                </InfinitePagination>
            </Card>
        </section>
    </div>
</template>
