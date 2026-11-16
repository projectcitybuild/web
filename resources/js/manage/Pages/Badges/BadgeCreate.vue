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
    <ManageFormPage title="Create a Badge" description="Badges are publicly displayable accolades that can be awarded to players">
        <BadgeForm :submit="submit" />
    </ManageFormPage>
</template>
