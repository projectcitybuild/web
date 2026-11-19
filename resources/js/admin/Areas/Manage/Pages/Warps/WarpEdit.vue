<script setup lang="ts">
import ManageFormPage from '../../../../Components/ManageFormPage.vue'
import WarpForm from './Partials/WarpForm.vue'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/Warps/WarpEdit']>()

function submit(form: Parameters<InstanceType<typeof WarpForm>['$props']['submit']>[0]) {
    form.put('/manage/minecraft/warps/' + props.warp.id)
}
</script>

<template>
    <ManageFormPage title="Edit Warp" description="Update this Minecraft teleport location.">
        <WarpForm :warp="props.warp" :submit="submit" />
    </ManageFormPage>
</template>
