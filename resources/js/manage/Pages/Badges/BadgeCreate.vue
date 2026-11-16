<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import BadgeForm from './Partials/BadgeForm.vue'
import { Badge } from '../../Data/Badge'

function submit(form: InertiaForm<Badge>) {
    form.post('/manage/badges')
}
</script>

<template>
    <ManageFormPage title="Create Badge" description="Create a player accolade that can be displayed publicly." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Badges', href: '/manage/badges' }, { label: 'Create' }]">
        <BadgeForm :submit="submit" />
    </ManageFormPage>
</template>
