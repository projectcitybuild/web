<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import DateTimePicker from '../../../Components/DateTimePicker.vue'
import PlayerPicker from '../../../Components/PlayerPicker.vue'
import DangerZone from '../../../Components/Forms/DangerZone.vue'
import FormActions from '../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../Components/Forms/FormField.vue'
import FormSection from '../../../Components/Forms/FormSection.vue'
import FormNotice from '../../../Components/Forms/FormNotice.vue'
import Textarea from '../../../../shared/Components/Textarea.vue'
import Input from '../../../../shared/Components/Input.vue'
import { useUnsavedChangesWarning } from '../../../Components/Forms/useUnsavedChangesWarning'

interface Props {
    ban?: App.Http.Data.AdminGameIPBanData,
    submit: (form: InertiaForm<IPBanFormData>) => void,
}

interface IPBanFormData {
    ip_address: string,
    reason: string,
    banner_uuid: string | null,
    banner_alias: string | null,
    created_at: Date,
}

const props = defineProps<Props>()

const form = useForm<IPBanFormData>('IPBanForm', {
    ip_address: props.ban?.ip_address ?? '',
    reason: props.ban?.reason ?? '',
    banner_uuid: props.ban?.banner_player?.uuid ?? null,
    banner_alias: props.ban?.banner_player?.alias ?? null,
    created_at: props.ban?.created_at
        ? new Date(props.ban.created_at)
        : new Date(),
})

const isEdit = computed(() => props.ban != null)
useUnsavedChangesWarning(form)

function submit(): void {
    props.submit(form)
}

function destroy(): void {
    if (props.ban) {
        form.delete(`/manage/ip-bans/${props.ban.id}`)
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Ban Details"
            description="Set the IP address, date, and reason for the ban."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="ip_address"
                    label="IP Address"
                    :error="form.errors.ip_address"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.ip_address" placeholder="192.168.0.1" />
                </FormField>
                <FormField
                    id="created_at"
                    label="Date of Ban"
                    :error="form.errors.created_at ?? undefined"
                    required
                    v-slot="field"
                >
                    <DateTimePicker
                        :id="field.id"
                        v-model="form.created_at"
                        aria-label="Date of Ban"
                        :aria-invalid="field.ariaInvalid"
                        :aria-describedby="form.errors.created_at ? field.id + '-error' : undefined"
                        @change="form.created_at = $event"
                    />
                </FormField>
                <FormField
                    id="banner_uuid"
                    label="Banned By"
                    description="Leaving this empty will show it as banned by System."
                    :error="form.errors.banner_uuid"
                >
                    <PlayerPicker
                        id="banner_uuid"
                        v-model:uuid="form.banner_uuid"
                        v-model:alias="form.banner_alias"
                    />
                </FormField>
                <FormField
                    id="reason"
                    label="Reason for Ban"
                    description="This is the message shown to the player - keep it short and concise. Do not tell them to appeal; this is already appended to the end automatically."
                    :error="form.errors.reason"
                    required
                    class="sm:col-span-2"
                    v-slot="field"
                >
                    <Textarea
                        v-bind="field"
                        v-model="form.reason"
                        rows="3"
                        placeholder="eg. Repeated and intentional griefing of builds"
                    />
                </FormField>
            </div>
        </FormSection>

        <FormNotice type="info">
            The player will be kicked with a ban message if they are currently on the server.
        </FormNotice>

        <FormActions
            :submit-label="isEdit ? 'Update IP ban' : 'Create IP ban'"
            cancel-href="/manage/ip-bans"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete IP ban"
            description="Deleting should be reserved for cleaning up invalid or inappropriate bans, not for unbanning purposes."
            confirm-message="This cannot be undone. Deleting should be reserved for cleaning up invalid/inappropriate bans, not for unbanning purposes."
            confirm-title="Yes, Delete It"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
