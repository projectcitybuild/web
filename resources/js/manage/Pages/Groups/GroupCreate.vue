<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import GroupForm from './Partials/GroupForm.vue'
import BackButton from '../../Components/BackButton.vue'
import { Group } from '../../Data/Group'

function submit(form: InertiaForm<Group>) {
    form.post('/manage/groups')
}
</script>

<template>
    <section class="p-3 sm:p-5">
        <Head title="Create a Group"/>

        <div class="mx-auto max-w-screen-xl">
            <Card>
                <div class="p-8 max-w-2xl">
                    <BackButton href="/manage/groups" class="mb-4"/>

                    <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Group</h2>
                    <div class="text-sm text-gray-500">Groups are a collection of members with granted permissions</div>
                    <hr class="my-6"/>

                    <GroupForm :submit="submit"/>
                </div>
            </Card>
        </div>
    </section>
</template>
