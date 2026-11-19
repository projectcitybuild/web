<script setup lang="ts">
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import BadgeForm from './Partials/BadgeForm.vue'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Badges/BadgeEdit']>()

function submit(form: Parameters<InstanceType<typeof BadgeForm>['$props']['submit']>[0]) {
    form.put('/manage/badges/' + props.badge.id)
}
</script>

<template>
    <ManageFormPage title="Edit Badge" description="Update this player accolade and how it is displayed.">
        <BadgeForm :badge="props.badge" :submit="submit" />
    </ManageFormPage>
</template>
