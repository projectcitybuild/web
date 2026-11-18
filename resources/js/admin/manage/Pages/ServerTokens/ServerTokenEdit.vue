<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import ServerTokenForm from './Partials/ServerTokenForm.vue'
import type { ServerToken } from '../../Data/ServerToken'

interface Props {
    token: ServerToken,
}

const props = defineProps<Props>()

function submit(form: Parameters<InstanceType<typeof ServerTokenForm>['$props']['submit']>[0]) {
    form.put('/manage/server-tokens/' + props.token.id)
}
</script>

<template>
    <ManageFormPage title="Edit Server Token" description="Update this API credential's name and access settings.">
        <ServerTokenForm :token="props.token" :submit="submit" />
    </ManageFormPage>
</template>
