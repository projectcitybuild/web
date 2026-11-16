<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import { Home } from '../../Data/Home';
import HomeForm from './Partials/HomeForm.vue';

interface Props {
    home: Home,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Home>) {
    form.put('/manage/homes/' + props.home.id)
}
</script>

<template>
    <ManageFormPage title="Edit Home" description="Update this saved Minecraft player location." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Minecraft' }, { label: 'Homes', href: '/manage/homes' }, { label: 'Edit' }]">
        <HomeForm :home="props.home" :submit="submit" />
    </ManageFormPage>
</template>
