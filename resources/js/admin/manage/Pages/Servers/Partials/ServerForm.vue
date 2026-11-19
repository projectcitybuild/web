<script setup lang="ts">
import { Routes } from '../../../../Routes'
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import DangerZone from '../../../Components/Forms/DangerZone.vue'
import FormActions from '../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../Components/Forms/FormField.vue'
import FormSection from '../../../Components/Forms/FormSection.vue'
import Input from '../../../../shared/Components/Input.vue'
import { useUnsavedChangesWarning } from '../../../Components/Forms/useUnsavedChangesWarning'

interface Props {
    server?: App.Http.Data.AdminServerData,
    submit: (form: InertiaForm<ServerFormData>) => void,
}

interface ServerFormData {
    name: string,
    ip: string,
    port: number | string,
    web_port: number | string | null,
}

const props = defineProps<Props>()

const form = useForm<ServerFormData>({
    name: props.server?.name ?? '',
    ip: props.server?.ip ?? '',
    port: props.server?.port ?? '',
    web_port: props.server?.web_port ?? null,
})

const isEdit = computed(() => props.server != null)
useUnsavedChangesWarning(form)

function submit(): void {
    props.submit(form)
}

function destroy(): void {
    if (props.server) {
        form.delete(`/manage/servers/${props.server.id}`)
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Server Connection"
            description="Configure the Minecraft server address and webhook port."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="name"
                    label="Name"
                    :error="form.errors.name"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.name" />
                </FormField>
                <FormField
                    id="ip"
                    label="IP Address"
                    :error="form.errors.ip"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.ip" placeholder="192.168.0.1" />
                </FormField>
                <FormField
                    id="port"
                    label="Port"
                    description="The Minecraft server port."
                    :error="form.errors.port"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.port" placeholder="25565" />
                </FormField>
                <FormField
                    id="web_port"
                    label="Web Port"
                    description="The web port to send webhook events to."
                    :error="form.errors.web_port"
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.web_port" placeholder="8080" />
                </FormField>
            </div>
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update server' : 'Create server'"
            :cancel-href="Routes.manage.servers.list"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete server"
            description="Permanently remove this server configuration."
            confirm-message="This cannot be undone."
            confirm-title="Yes, Delete It"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
