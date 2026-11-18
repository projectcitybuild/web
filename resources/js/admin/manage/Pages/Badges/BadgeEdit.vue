<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import BadgeForm from './Partials/BadgeForm.vue'
import type { Badge } from '../../Data/Badge'

interface Props {
    badge: Badge,
}

const props = defineProps<Props>()

function submit(form: Parameters<InstanceType<typeof BadgeForm>['$props']['submit']>[0]) {
    form.put('/manage/badges/' + props.badge.id)
}
</script>

<template>
    <ManageFormPage title="Edit Badge" description="Update this player accolade and how it is displayed.">
        <BadgeForm :badge="props.badge" :submit="submit" />
    </ManageFormPage>
</template>
