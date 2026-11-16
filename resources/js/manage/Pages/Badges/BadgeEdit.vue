<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import BadgeForm from './Partials/BadgeForm.vue'
import type { Badge } from '../../Data/Badge'

interface Props {
    badge: Badge,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<Badge>) {
    form.put('/manage/badges/' + props.badge.id)
}
</script>

<template>
    <ManageFormPage title="Edit Badge" description="Update this player accolade and how it is displayed." :breadcrumbs="[{ label: 'Manage', href: '/manage' }, { label: 'Badges', href: '/manage/badges' }, { label: 'Edit' }]">
        <BadgeForm :badge="props.badge" :submit="submit" />
    </ManageFormPage>
</template>
