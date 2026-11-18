<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import type { ServerToken } from '../../../Data/ServerToken'
import DangerZone from '../../../Components/Forms/DangerZone.vue'
import FormActions from '../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../Components/Forms/FormField.vue'
import FormSection from '../../../Components/Forms/FormSection.vue'
import Input from '../../../../shared/Components/Input.vue'
import Textarea from '../../../../shared/Components/Textarea.vue'
import { useUnsavedChangesWarning } from '../../../Components/Forms/useUnsavedChangesWarning'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'
import FilledButton from '../../../Components/FilledButton.vue'

interface Props {
    token?: ServerToken,
    submit: (form: InertiaForm<ServerTokenFormData>) => void,
}

interface ServerTokenFormData {
    description: string,
    token: string,
    allowed_ips: string,
}

const props = defineProps<Props>()

const form = useForm<ServerTokenFormData>({
    description: props.token?.description ?? '',
    token: props.token?.token ?? generate(),
    allowed_ips: props.token?.allowed_ips?.split(',').join('\n') ?? '',
})

const isEdit = computed(() => props.token != null)
useUnsavedChangesWarning(form)

function generate(): string {
    const length = 32
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
    let result = ''
    const randomArray = new Uint8Array(length)
    crypto.getRandomValues(randomArray)
    randomArray.forEach((number) => {
        result += chars[number % chars.length]
    })
    return result
}

function submit(): void {
    props.submit(form)
}

function destroy(): void {
    if (props.token) {
        form.delete(`/manage/server-tokens/${props.token.id}`)
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Token Details"
            description="Configure the token description, credential, and allowed IP addresses."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="description"
                    label="Description"
                    description="Write a description that notes what is consuming the token (eg. PCBridge)."
                    :error="form.errors.description"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.description" placeholder="For PCBridge" />
                </FormField>
                <FormField
                    id="token"
                    label="Access Token"
                    :error="form.errors.token"
                    required
                    v-slot="field"
                >
                    <div class="flex gap-2">
                        <div class="min-w-0 flex-1">
                            <Input v-bind="field" v-model="form.token" />
                        </div>
                        <FilledButton
                            type="button"
                            variant="danger"
                            aria-label="Generate a new access token"
                            @click="form.token = generate()"
                        >
                            <SvgIcon :svg="Icons.refresh" />
                        </FilledButton>
                    </div>
                </FormField>
                <FormField
                    id="allowed_ips"
                    label="Allowed IPs"
                    description="IP addresses permitted to use this token to hit the API, one per line. Leave blank if any IP address is allowed."
                    :error="form.errors.allowed_ips"
                    class="sm:col-span-2"
                    v-slot="field"
                >
                    <Textarea v-bind="field" v-model="form.allowed_ips" rows="4" />
                </FormField>
            </div>
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update token' : 'Create token'"
            cancel-href="/manage/server-tokens"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete server token"
            description="Permanently revoke and remove this server token."
            confirm-message="This cannot be undone."
            confirm-title="Yes, Delete It"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
