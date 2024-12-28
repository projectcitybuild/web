<script setup lang="ts">
import {Head, InertiaForm} from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import GroupForm from './Partials/GroupForm.vue'
import BackButton from '../../Components/BackButton.vue'
import type { Group } from '../../Data/Group'

interface Props {
    group: Group,
}
const props = defineProps<Props>()

function submit(form: InertiaForm<Group>) {
    form.put('/manage/groups/' + props.group.group_id)
}
</script>

<template>
    <section class="p-3 sm:p-5">
        <Head title="Edit a Badge" />

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <BackButton href="/manage/badges" class="mb-4" />

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Edit a Group</h2>
                    <div class="text-sm text-gray-500">Groups are a collection of members with granted permissions</div>
                    <hr class="my-6" />

                    <GroupForm :group="props.group" :submit="submit" />
                </div>
            </Card>
        </div>
    </section>
</template>
