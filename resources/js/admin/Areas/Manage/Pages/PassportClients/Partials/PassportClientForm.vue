<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import { Routes } from '../../../../../Navigation/Routes'
import Checkbox from '../../../../../Components/Checkbox.vue'
import ConfirmDialog from '../../../../../Components/ConfirmDialog.vue'
import DangerZone from '../../../../../Components/Forms/DangerZone.vue'
import FilledButton from '../../../../../Components/FilledButton.vue'
import FormActions from '../../../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../../../Components/Forms/FormField.vue'
import FormSection from '../../../../../Components/Forms/FormSection.vue'
import Input from '../../../../../Components/Input.vue'
import Select from '../../../../../Components/Select.vue'
import Textarea from '../../../../../Components/Textarea.vue'
import SvgIcon from '../../../../../Components/SvgIcon.vue'
import { Icons } from '../../../../../Components/SvgIcon.vue'
import { useUnsavedChangesWarning } from '../../../../../Components/Forms/useUnsavedChangesWarning'

interface PassportClient {
    id: number,
    name: string,
    grant_types: string[],
    redirect_uris: string[],
    confidential: boolean,
    revoked: boolean,
}

export interface PassportClientFormData {
    name: string,
    grant_type: 'authorization_code' | 'client_credentials',
    confidential: boolean,
    redirect_uris: string,
}

interface Props {
    client?: PassportClient,
    submit: (form: InertiaForm<PassportClientFormData>) => void,
}

const props = defineProps<Props>()
const isEdit = computed(() => props.client != null)
const secretForm = useForm({})
const secretConfirmDialog = ref<InstanceType<typeof ConfirmDialog>>()
const form = useForm<PassportClientFormData>({
    name: props.client?.name ?? '',
    grant_type: props.client?.grant_types.includes('authorization_code')
        ? 'authorization_code'
        : 'client_credentials',
    confidential: props.client?.confidential ?? true,
    redirect_uris: props.client?.redirect_uris.join('\n') ?? '',
})

const isRedirectUriClient = computed(() => {
    if (props.client) {
        return props.client.grant_types.includes('authorization_code')
            || props.client.redirect_uris.length > 0
    }

    return form.grant_type === 'authorization_code'
})
const redirectUriError = computed(() => {
    if (form.errors.redirect_uris) {
        return form.errors.redirect_uris
    }

    return Object.entries(form.errors).find(([field]) => field.startsWith('redirect_uris.'))?.[1]
})

watch(() => form.grant_type, (grantType) => {
    if (grantType === 'client_credentials') {
        form.redirect_uris = ''
    }
})

useUnsavedChangesWarning(form)

function submit(): void {
    if (!form.processing) {
        props.submit(form)
    }
}

function destroy(): void {
    if (props.client && !form.processing) {
        form.delete(Routes.manage.passportClients.destroy(props.client.id))
    }
}

function regenerateSecret(): void {
    if (props.client && props.client.confidential && !secretForm.processing) {
        secretConfirmDialog.value?.close()
        secretForm.post(Routes.manage.passportClients.regenerateSecret(props.client.id), {
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Client details"
            description="Grant type and client confidentiality cannot be changed after creation."
        >
            <div class="grid gap-4">
                <FormField
                    id="name"
                    label="Name"
                    description="A descriptive name to identify this OAuth client."
                    :error="form.errors.name"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.name" maxlength="191" />
                </FormField>

                <FormField
                    v-if="!isEdit"
                    id="grant_type"
                    label="Grant type"
                    description="Choose authorization code for user sign-in, or client credentials for service-to-service access."
                    :error="form.errors.grant_type"
                    required
                    v-slot="field"
                >
                    <Select v-bind="field" v-model="form.grant_type">
                        <option value="authorization_code">Authorization code (with refresh tokens)</option>
                        <option value="client_credentials">Client credentials (service-to-service)</option>
                    </Select>
                </FormField>

                <p v-else class="text-sm text-muted">
                    Grant types: <span class="font-medium text-foreground">{{ client?.grant_types.join(', ') }}</span>
                </p>

                <Checkbox
                    v-if="!isEdit && isRedirectUriClient"
                    id="confidential"
                    v-model="form.confidential"
                    label="Confidential client"
                    description="Leave enabled when the client can safely store a secret. Disable for a public PKCE client."
                    :error="form.errors.confidential"
                />
            </div>
        </FormSection>

        <FormSection
            v-if="isRedirectUriClient"
            title="Redirect URIs"
            description="Add one exact callback URL per line. Passport will only redirect to these registered URLs."
        >
            <FormField
                id="redirect_uris"
                label="Allowed redirect URIs"
                :error="redirectUriError"
                required
                v-slot="field"
            >
                <Textarea v-bind="field" v-model="form.redirect_uris" rows="5" spellcheck="false" />
            </FormField>
        </FormSection>

        <FormSection
            v-if="client?.confidential"
            title="Client secret"
            description="Regenerating the secret immediately invalidates the current one. The new secret will be shown once after regeneration."
        >
            <FilledButton
                type="button"
                variant="secondary"
                :loading="secretForm.processing"
                :disabled="secretForm.processing"
                @click="secretConfirmDialog?.open()"
            >
                <SvgIcon :svg="Icons.refresh" aria-hidden="true" />
                Regenerate secret
            </FilledButton>
            <ConfirmDialog
                ref="secretConfirmDialog"
                title="Regenerate client secret?"
                message="The current client secret will stop working immediately. Any applications using it must be updated with the new secret."
                confirm-title="Regenerate secret"
                confirm-variant="primary"
                :on-confirm="regenerateSecret"
            />
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update client' : 'Create client'"
            :cancel-href="Routes.manage.passportClients.list"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="client"
            title="Revoke"
            description="Passport will revoke this client and its active tokens. The revoked client record remains available for history."
            confirm-message="This client and its active tokens will be revoked. Continue?"
            confirm-title="Yes, revoke client"
            button-label="Revoke client"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
