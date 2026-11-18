<script setup lang="ts">
import ManageFormPage from '../../Components/ManageFormPage.vue'
import ServerForm from './Partials/ServerForm.vue'
import type { Server } from '../../Data/Server'

interface Props {
    server: Server,
}

const props = defineProps<Props>()

function submit(form: Parameters<InstanceType<typeof ServerForm>['$props']['submit']>[0]) {
    form.put('/manage/servers/' + props.server.id)
}
</script>

<template>
    <ManageFormPage title="Edit Server" description="Update this server's registration and integration settings.">
        <ServerForm :server="props.server" :submit="submit" />
    </ManageFormPage>
</template>
