<script setup lang="ts">
import { Routes } from '../../../../Routes'
import { InertiaForm, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import PlayerPicker from '../../../Components/PlayerPicker.vue'
import ChoiceCard from '../../../Components/Forms/ChoiceCard.vue'
import FormActions from '../../../Components/Forms/FormActions.vue'
import FormErrorSummary from '../../../Components/Forms/FormErrorSummary.vue'
import FormField from '../../../Components/Forms/FormField.vue'
import FormSection from '../../../Components/Forms/FormSection.vue'
import DateTimeInput from '../../../Components/Forms/DateTimeInput.vue'
import Input from '../../../../shared/Components/Input.vue'
import { useUnsavedChangesWarning } from '../../../Components/Forms/useUnsavedChangesWarning'

interface Props {
    player?: App.Http.Data.MinecraftPlayerData,
    submit: (form: InertiaForm<PlayerFormData>) => void,
}

interface PlayerFormData {
    uuid: string | null,
    alias: string | null,
    nickname: string | null,
    account_id: number | string | null,
    created_at: Date,
}

const props = defineProps<Props>()

const form = useForm<PlayerFormData>({
    uuid: props.player?.uuid ?? null,
    alias: props.player?.alias ?? null,
    nickname: props.player?.nickname ?? null,
    account_id: props.player?.account_id ?? null,
    created_at: props.player?.created_at
        ? new Date(props.player.created_at)
        : new Date(),
})

const isEdit = computed(() => props.player != null)
const entryMode = ref<'search' | 'manual'>(isEdit.value ? 'manual' : 'search')
const uuidParam = new URLSearchParams(usePage().url.split('?')[1] ?? '').get('uuid') ?? undefined
useUnsavedChangesWarning(form)

function submit(): void {
    if (!form.processing) {
        props.submit(form)
    }
}
</script>

<template>
    <form class="grid gap-6" @submit.prevent="submit">
        <FormErrorSummary :errors="form.errors" />

        <FormSection
            title="Player Identification"
            description="Find an existing Minecraft player or enter their UUID and last known alias manually."
        >
            <fieldset class="grid gap-3">
                <legend class="text-sm font-medium text-foreground">Identification method</legend>
                <div class="grid gap-3 sm:grid-cols-2">
                    <ChoiceCard
                        v-model="entryMode"
                        name="player-entry-mode"
                        value="search"
                        title="Search for a player"
                        description="Look up a Minecraft username or UUID."
                    />
                    <ChoiceCard
                        v-model="entryMode"
                        name="player-entry-mode"
                        value="manual"
                        title="Enter player details"
                        description="Use this when the player cannot be found by search."
                    />
                </div>
            </fieldset>

            <div v-if="entryMode === 'search'" class="grid gap-3">
                <FormField
                    id="uuid"
                    label="Find a player"
                    description="Choose a result to fill in the UUID and last known alias."
                >
                    <PlayerPicker
                        id="uuid"
                        v-model:uuid="form.uuid"
                        v-model:alias="form.alias"
                        :initial-search="uuidParam"
                    />
                </FormField>
            </div>
            <div v-else class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="uuid"
                    label="Minecraft UUID"
                    description="The player's unique Minecraft identifier."
                    :error="form.errors.uuid"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.uuid" autocomplete="off" />
                </FormField>
                <FormField
                    id="alias"
                    label="Last known alias"
                    description="The most recent Minecraft username for this UUID."
                    :error="form.errors.alias"
                    required
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.alias" autocomplete="off" />
                </FormField>
            </div>
            <p
                v-if="entryMode === 'search' && (form.errors.uuid || form.errors.alias)"
                class="text-sm text-danger"
                role="alert"
            >
                {{ form.errors.uuid || form.errors.alias }}
            </p>
        </FormSection>

        <FormSection
            title="Player Profile"
            description="Set optional display and account information."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <FormField
                    id="nickname"
                    label="In-game nickname"
                    description="Optional nickname displayed for this player in the website."
                    :error="form.errors.nickname"
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.nickname" />
                </FormField>
                <FormField
                    id="account_id"
                    label="Associated account ID"
                    description="Leave blank if this Minecraft player is not linked to a website account."
                    :error="form.errors.account_id"
                    v-slot="field"
                >
                    <Input v-bind="field" v-model="form.account_id" type="number" min="1" step="1" />
                </FormField>
            </div>
        </FormSection>

        <FormSection
            title="Registration Metadata"
            description="Set when this player was first registered."
        >
            <DateTimeInput
                id="created_at"
                label="First seen"
                :error="form.errors.created_at"
                required
                v-model="form.created_at"
            />
        </FormSection>

        <FormActions
            :submit-label="isEdit ? 'Update player' : 'Create player'"
            :cancel-href="Routes.manage.players.list"
            :processing="form.processing"
            sticky
        />
    </form>
</template>
