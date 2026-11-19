<script setup lang="ts">
import { InertiaForm, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import { UnbanType } from '../../../../../generated/openapi-enums'
import DateTimePicker from '../../../Components/DateTimePicker.vue'
import PlayerPicker from '../../../Components/PlayerPicker.vue'
import DangerZone from '../../../Components/Forms/DangerZone.vue'
import FormActions from '../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../Components/Forms/FormField.vue'
import FormSection from '../../../Components/Forms/FormSection.vue'
import FormNotice from '../../../Components/Forms/FormNotice.vue'
import Textarea from '../../../../shared/Components/Textarea.vue'
import Select from '../../../../shared/Components/Select.vue'
import { useUnsavedChangesWarning } from '../../../Components/Forms/useUnsavedChangesWarning'

interface Props {
    ban?: App.Http.Data.AdminGamePlayerBanData,
    submit: (form: InertiaForm<PlayerBanFormData>) => void,
}

interface PlayerBanFormData {
    banned_uuid: string | null,
    banned_alias: string | null,
    banner_uuid: string | null,
    banner_alias: string | null,
    created_at: Date,
    expires_at: Date | null,
    reason: string,
    additional_info: string,
    unbanned_at: Date | null,
    unbanner_uuid: string | null,
    unbanner_alias: string | null,
    unban_type: App.Domains.Bans.Data.UnbanType | null,
}

const props = defineProps<Props>()

const uuidParam = new URLSearchParams(window.location.search).get('uuid') ?? undefined

const form = useForm<PlayerBanFormData>({
    banned_uuid: props.ban?.banned_player.uuid ?? null,
    banned_alias: props.ban?.banned_player.alias ?? null,
    banner_uuid: props.ban?.banner_player?.uuid ?? null,
    banner_alias: props.ban?.banner_player?.alias ?? null,
    created_at: props.ban?.created_at
        ? new Date(props.ban.created_at)
        : new Date(),
    expires_at: props.ban?.expires_at
        ? new Date(props.ban.expires_at)
        : null,
    reason: props.ban?.reason ?? '',
    additional_info: props.ban?.additional_info ?? '',
    unbanned_at: props.ban?.unbanned_at
        ? new Date(props.ban.unbanned_at)
        : null,
    unbanner_uuid: props.ban?.unbanner_player?.uuid ?? null,
    unbanner_alias: props.ban?.unbanner_player?.alias ?? null,
    unban_type: props.ban?.unban_type ?? null,
})

const isEdit = computed(() => props.ban != null)
useUnsavedChangesWarning(form)

function submit(): void {
    props.submit(form)
}

function destroy(): void {
    if (props.ban) {
        form.delete(`/manage/player-bans/${props.ban.id}`)
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Ban Details"
            description="Select the player and record the ban dates, reason, and staff information."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="banned_uuid"
                    label="Player"
                    :error="form.errors.banned_uuid"
                    required
                    class="sm:col-span-2"
                >
                    <PlayerPicker
                        id="banned_uuid"
                        v-model:uuid="form.banned_uuid"
                        v-model:alias="form.banned_alias"
                        :initial-search="uuidParam"
                    />
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
                    id="expires_at"
                    label="Expires At"
                    :error="form.errors.expires_at ?? undefined"
                    v-slot="field"
                >
                    <DateTimePicker
                        :id="field.id"
                        v-model="form.expires_at"
                        aria-label="Expires At"
                        :aria-invalid="field.ariaInvalid"
                        :aria-describedby="form.errors.expires_at ? field.id + '-error' : undefined"
                        @change="form.expires_at = $event"
                    />
                </FormField>
                <FormField
                    id="banner_uuid"
                    label="Banned By"
                    description="Leaving this empty will show it as banned by System."
                    :error="form.errors.banner_uuid ?? undefined"
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
                <FormField
                    id="additional_info"
                    label="Additional Information"
                    description="Optional field to record full or extra details about the offence, notes in case they appeal, etc. This will only be visible to staff."
                    :error="form.errors.additional_info"
                    class="sm:col-span-2"
                    v-slot="field"
                >
                    <Textarea
                        v-bind="field"
                        v-model="form.additional_info"
                        rows="9"
                        placeholder="eg. Smashed over 500 blocks of Notch's house in creative. Witnessed use of an auto-clicker"
                    />
                </FormField>
            </div>
        </FormSection>

        <FormSection
            v-if="isEdit"
            title="Unban Information"
            description="Only fill this in to unban."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="unbanned_at"
                    label="Date of Unban"
                    description="This is just for book-keeping. If any date is filled here, it will be considered as unbanned."
                    :error="form.errors.unbanned_at"
                    v-slot="field"
                >
                    <DateTimePicker
                        :id="field.id"
                        v-model="form.unbanned_at"
                        aria-label="Date of Unban"
                        :aria-invalid="field.ariaInvalid"
                        :aria-describedby="form.errors.unbanned_at ? field.id + '-error' : undefined"
                        @change="form.unbanned_at = $event"
                    />
                </FormField>
                <FormField
                    id="unban_type"
                    label="Unban Type"
                    :error="form.errors.unban_type"
                    v-slot="field"
                >
                    <Select v-bind="field" v-model="form.unban_type">
                        <option :value="null">Select</option>
                        <option :value="UnbanType.expired">Expired</option>
                        <option :value="UnbanType.manual">Manual</option>
                        <option :value="UnbanType.appealed">Appealed</option>
                    </Select>
                </FormField>
                <FormField
                    id="unbanner_uuid"
                    label="Unbanned By"
                    :error="form.errors.unbanner_uuid"
                    class="sm:col-span-2"
                >
                    <PlayerPicker
                        id="unbanner_uuid"
                        v-model:uuid="form.unbanner_uuid"
                        v-model:alias="form.unbanner_alias"
                    />
                </FormField>
            </div>
        </FormSection>

        <FormNotice type="info">
            The player will be kicked with a ban message if they are currently on the server.
        </FormNotice>

        <FormActions
            :submit-label="isEdit ? 'Update player ban' : 'Create player ban'"
            cancel-href="/manage/player-bans"
            :processing="form.processing"
            sticky
        />

        <DangerZone
            v-if="isEdit"
            title="Delete player ban"
            description="Deleting should be reserved for cleaning up invalid or inappropriate bans, not for unbanning purposes."
            confirm-message="This cannot be undone. Deleting should be reserved for cleaning up invalid/inappropriate bans, not for unbanning purposes."
            confirm-title="Yes, Delete It"
            :disabled="form.processing"
            @confirm="destroy"
        />
    </form>
</template>
