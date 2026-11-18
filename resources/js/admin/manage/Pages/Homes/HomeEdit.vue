<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import HomeForm from './Partials/HomeForm.vue'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Homes/HomeEdit']>()

function submit(form: Parameters<InstanceType<typeof HomeForm>['$props']['submit']>[0]) {
    form.put('/manage/homes/' + props.home.id)
}
</script>

<template>
    <ManageFormPage title="Edit Home" description="Update this saved Minecraft player location.">
        <HomeForm :home="props.home" :submit="submit" />
    </ManageFormPage>
</template>
