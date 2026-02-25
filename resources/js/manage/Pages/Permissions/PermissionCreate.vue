<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import PermissionForm from './Partials/PermissionForm.vue'
import { Permission } from '../../Data/Permission'

function submit(form: InertiaForm<Permission>) {
    form.post('/manage/permissions')
}
</script>

<template>
    <section>
        <Head title="Create a Permission"/>

        <Card>
            <div class="p-8 max-w-2xl">
                <BackButton href="/manage/permissions" class="mb-4"/>

                <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Create a Permission</h2>
                <div class="text-sm text-gray-500">Permissions allow a role access to a resource</div>
                <hr class="my-6"/>

                <PermissionForm :submit="submit"/>
            </div>
        </Card>
    </section>
</template>
