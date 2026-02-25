<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
import { Permission } from '../../Data/Permission';
import PermissionForm from './Partials/PermissionForm.vue';

interface Props {
    permission: Permission,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Permission>) {
    form.put('/manage/permissions/' + props.permission.id)
}
</script>

<template>
    <section>
        <Head title="Edit a Permission"/>

        <Card>
            <div class="p-8 max-w-2xl">
                <BackButton href="/manage/permissions" class="mb-4"/>

                <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Edit a Permission</h2>
                <div class="text-sm text-gray-500">Permissions allow a role access to a resource</div>
                <hr class="my-6"/>

                <PermissionForm :permission="props.permission" :submit="submit"/>
            </div>
        </Card>
    </section>
</template>
