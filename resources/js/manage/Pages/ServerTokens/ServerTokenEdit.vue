<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import ServerTokenForm from './Partials/ServerTokenForm.vue'
import type { ServerToken } from '../../Data/ServerToken'

interface Props {
    token: ServerToken,
}

const props = defineProps<Props>()

function submit(form: InertiaForm<ServerToken>) {
    form.put('/manage/server-tokens/' + props.token.id)
}
</script>

<template>
    <ManageFormPage title="Edit a Server Token" description="Creates an authorization token that grants access to the PCB API. For use with first-party applications only">
        <ServerTokenForm :token="props.token" :submit="submit" />
    </ManageFormPage>
</template>
