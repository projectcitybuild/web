<script setup lang="ts">
import DateTimePicker from '../../../Components/DateTimePicker.vue'
import PlayerPicker from '../../../Components/PlayerPicker.vue'
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { PlayerBan, UnbanType } from '../../../Data/PlayerBan'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'

interface Props {
    ban?: PlayerBan,
    submit: Function,
}
const props = defineProps<Props>()

const params = new URLSearchParams(window.location.search)
const uuidParam = params.get('uuid')

const form = useForm<PlayerBan>({
    banned_uuid: props.ban?.banned_player.uuid,
    banned_alias: props.ban?.banned_player.alias,
    banner_uuid: props.ban?.banner_player?.uuid,
    banner_alias: props.ban?.banner_player?.alias,
    created_at: props.ban?.created_at
        ? new Date(props.ban.created_at)
        : new Date(),
    expires_at: props.ban?.expires_at
        ? new Date(props.ban.expires_at)
        : null,
    reason: props.ban?.reason,
    additional_info: props.ban?.additional_info,
    unbanned_at: props.ban?.unbanned_at
        ? new Date(props.ban.unbanned_at)
        : null,
    unbanner_uuid: props.ban?.unbanner_player?.uuid,
    unbanner_alias: props.ban?.unbanner_player?.alias,
    unban_type: props.ban?.unban_type,
})

const deleteModal = ref<InstanceType<typeof ConfirmDialog>>()
const isEdit = computed(() => props.ban != null)

function submit() {
    props.submit(form)
}

function destroy() {
    deleteModal.value?.close()
    form.delete('/manage/player-bans/' + props.ban.id)
}
</script>

<template>
    <form @submit.prevent="submit">
        <ConfirmDialog
            ref="deleteModal"
            message="This cannot be undone. Deleting should be reserved for cleaning up invalid/inappropriate bans, not for unbanning purposes."
            confirm-title="Yes, Delete It"
            :on-confirm="destroy"
        />

        <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
            <div class="col-span-2">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Player<span class="text-red-500">*</span>
                </label>
                <PlayerPicker
                    v-model:uuid="form.banned_uuid"
                    v-model:alias="form.banned_alias"
                    :initial-search="uuidParam"
                />
                <div v-if="form.errors.banned_uuid" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.banned_uuid }}
                </div>
            </div>
            <div>
                <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Date of Ban<span class="text-red-500">*</span>
                </label>
                <DateTimePicker
                    v-model="form.created_at"
                    @change="form.created_at = $event"
                />
                <div v-if="form.errors.created_at" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.created_at }}
                </div>
            </div>
            <div>
                <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Expires At
                </label>
                <DateTimePicker
                    v-model="form.expires_at"
                    @change="form.expires_at = $event"
                />
                <div v-if="form.errors.expires_at" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.expires_at }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Banned By</label>
                <PlayerPicker
                    v-model:uuid="form.banner_uuid"
                    v-model:alias="form.banner_alias"
                />
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    Leaving this empty will show it as banned by System
                </span>
                <div v-if="form.errors.banner_uuid" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.banner_uuid }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Reason for Ban<span class="text-red-500">*</span>
                </label>
                <textarea
                    v-model="form.reason"
                    id="description"
                    rows="3"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="eg. Repeated and intentional griefing of builds"
                ></textarea>
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    This is the message shown to the player - keep it short and concise.<br/>
                    <strong>Do not tell them to appeal</strong>, this is already appended to the end automatically.
                </span>
                <div v-if="form.errors.reason" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.reason }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="additional_info" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Additional Information
                </label>
                <textarea
                    v-model="form.additional_info"
                    id="additional_info"
                    rows="9"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="eg. Smashed over 500 blocks of Notch's house in creative. Witnessed use of an auto-clicker"
                ></textarea>
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    Optional field to record full or extra details about the offence, notes in case they appeal, etc.
                    <strong>This will only be visible to staff</strong>
                </span>
                <div v-if="form.errors.additional_info" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.additional_info }}
                </div>
            </div>

            <template v-if="isEdit">
                <hr class="col-span-2" />

                <span class="col-span-2">Only fill this in to unban:</span>

                <div>
                    <label for="unbanned_at" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Date of Unban
                    </label>
                    <DateTimePicker
                        v-model="form.unbanned_at"
                        @change="form.unbanned_at = $event"
                    />
                    <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    This is just for book-keeping. If <i>any</i> date is filled here, it will be considered as unbanned
                </span>
                    <div v-if="form.errors.unbanned_at" class="text-xs text-red-500 font-bold mt-2">
                        {{ form.errors.unbanned_at }}
                    </div>
                </div>
                <div>
                    <label for="unban_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Unban Type
                    </label>
                    <select
                        v-model="form.unban_type"
                        id="unban_type"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    >
                        <option :value="null">Select</option>
                        <option :value="UnbanType.expired">Expired</option>
                        <option :value="UnbanType.manual">Manual</option>
                        <option :value="UnbanType.appealed">Appealed</option>
                    </select>
                    <div v-if="form.errors.unban_type" class="text-xs text-red-500 font-bold mt-2">
                        {{ form.errors.unban_type }}
                    </div>
                </div>

                <div class="col-span-2">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Unbanned By
                    </label>
                    <PlayerPicker
                        v-model:uuid="form.unbanner_uuid"
                        v-model:alias="form.unbanner_alias"
                    />
                    <div v-if="form.errors.unbanner_uuid" class="text-xs text-red-500 font-bold mt-2">
                        {{ form.errors.unbanner_uuid }}
                    </div>
                </div>

                <hr class="col-span-2" />
            </template>
        </div>

        <div class="flex flex-row gap-2 mt-8">
            <FilledButton
                type="submit"
                variant="primary"
                :disabled="form.processing"
            >
                <Spinner v-if="form.processing" />
                <template v-else>{{ isEdit ? 'Update' : 'Create' }}</template>
            </FilledButton>

            <OutlinedButton
                variant="danger"
                v-if="isEdit"
                type="button"
                @click="deleteModal.open()"
            >
                <SvgIcon icon="bin" />
                Delete
            </OutlinedButton>
        </div>

        <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
            Note: The player will be kicked with a ban message if they are currently on the server
        </span>
    </form>
</template>
