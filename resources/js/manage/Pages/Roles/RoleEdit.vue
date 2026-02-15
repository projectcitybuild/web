<script setup lang="ts">
import { Head, InertiaForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import RoleForm from './Partials/RoleForm.vue'
import BackButton from '../../Components/BackButton.vue'
import type { Role } from '../../Data/Role'

interface Props {
    role: Role,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Role>) {
    form.put('/manage/roles/' + props.role.id)
}
</script>

<template>
    <section>
        <Head title="Edit a Role"/>

        <Card>
            <div class="p-8 max-w-2xl">
                <BackButton href="/manage/roles" class="mb-4"/>

                <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Edit a Role</h2>
                <div class="text-sm text-gray-500">Roles are a collection of members with granted permissions</div>
                <hr class="my-6"/>

                <RoleForm :role="props.role" :submit="submit"/>
            </div>
        </Card>
    </section>
</template>
