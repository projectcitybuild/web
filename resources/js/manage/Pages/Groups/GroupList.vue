<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3'
import type { Group } from '../../Data/Group'
import GroupListTable from './Partials/GroupListTable.vue'
import SuccessAlert from '../../Components/SuccessAlert.vue'
import Card from '../../Components/Card.vue'
import FilledButton from '../../Components/FilledButton.vue'
import SvgIcon from '../../Components/SvgIcon.vue'
import SpinnerRow from '../../Components/SpinnerRow.vue'

interface Props {
    success?: string,
    groups?: Group[],
}

defineProps<Props>()
</script>

<template>
    <Head title="Manage Groups"/>

    <section>
        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <Card>
            <div class="flex flex-row items-center justify-between p-4">
                <div>
                    <span v-if="groups" class="text-sm text-gray-500">
                        Showing <strong>{{ groups.length }}</strong> of <strong>{{ groups?.length ?? 0 }}</strong>
                    </span>
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

            <Deferred data="groups">
                <template #fallback>
                    <SpinnerRow />
                </template>

                <GroupListTable :groups="groups" class="border-t border-gray-200" />
            </Deferred>
        </Card>
    </section>
</template>
