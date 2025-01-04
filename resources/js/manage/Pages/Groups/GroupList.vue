<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import { Paginated } from '../../Data/Paginated'
import type { Group } from '../../Data/Group'
import GroupListTable from './Partials/GroupListTable.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'

interface Props {
    success?: string,
    groups: Paginated<Group>,
}

defineProps<Props>()
</script>

<template>
    <Head title="Manage Groups"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <div class="mx-auto max-w-screen-xl">
            <Card class="overflow-hidden">
                <div
                    class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2">

                    </div>
                    <div>
                        <Link
                            href="/manage/groups/create"
                            as="button"
                        >
                            <FilledButton variant="primary">
                                <SvgIcon icon="plus" />
                                Create Group
                            </FilledButton>
                        </Link>
                    </div>
                </div>

                <GroupListTable :groups="groups" class="overflow-x-auto"/>
            </Card>
        </div>
    </section>
</template>
