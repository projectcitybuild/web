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
    <ManageFormPage title="Edit a Badge" description="Badges are publicly displayable accolades that can be awarded to players">
        <BadgeForm :badge="props.badge" :submit="submit" />
    </ManageFormPage>
</template>
