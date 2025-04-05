<script setup lang="ts">
import DateTimePicker from '../../../Components/DateTimePicker.vue'
import PlayerPicker from '../../../Components/PlayerPicker.vue'
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { IPBan } from '../../../Data/IPBan'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'

interface Props {
    ban?: IPBan,
    submit: Function,
}

const props = defineProps<Props>()

const form = useForm<IPBan>({
    ip_address: props.ban?.ip_address,
    reason: props.ban?.reason,
    banner_uuid: props.ban?.banner_player?.uuid,
    banner_alias: props.ban?.banner_player?.alias,
    created_at: props.ban?.created_at
        ? new Date(props.ban.created_at)
        : new Date(),
}, 'IPBanForm')

const deleteModal = ref<InstanceType<typeof ConfirmDialog>>()
const isEdit = computed(() => props.ban != null)

function submit() {
    props.submit(form)
}

function destroy() {
    deleteModal?.value.close()
    form.delete('/manage/ip-bans/' + props.ban.id)
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
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    IP Address<span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.ip_address"
                    id="ip"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="192.168.0.1"
                >
                <div v-if="form.errors.ip_address" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.ip_address }}
                </div>
            </div>
            <div class="col-span-2">
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
