<script setup lang="ts">
import DateTimePicker from '../../../Components/DateTimePicker.vue'
import PlayerPicker from '../../../Components/PlayerPicker.vue'
import { useForm } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue'
import { PlayerWarning } from '../../../Data/PlayerWarning'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'

interface Props {
    warning?: PlayerWarning,
    submit: Function,
}
const props = defineProps<Props>()

const params = new URLSearchParams(window.location.search)
const uuidParam = params.get('uuid')

const form = useForm<PlayerWarning>({
    warned_uuid: props.warning?.warned_player.uuid,
    warned_alias: props.warning?.warned_player.alias,
    warner_uuid: props.warning?.warner_player.uuid,
    warner_alias: props.warning?.warner_player.alias,
    reason: props.warning?.reason,
    additional_info: props.warning?.additional_info,
    created_at: props.warning?.created_at
        ? new Date(props.warning.created_at)
        : new Date(),
})

const deleteModal = ref<InstanceType<typeof ConfirmDialog>>()
const isEdit = computed(() => props.warning != null)

function submit() {
    props.submit(form)
}

function destroy() {
    deleteModal?.value.close()
    form.delete('/manage/warnings/' + props.warning.id)
}
</script>

<template>
    <form @submit.prevent="submit">
        <ConfirmDialog
            ref="deleteModal"
            message="This cannot be undone."
            confirm-title="Yes, Delete It"
            :on-confirm="destroy"
        />

        <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
            <div class="col-span-2">
                <label for="warned_uuid" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Player<span class="text-red-500">*</span>
                </label>
                <PlayerPicker
                    v-model:uuid="form.warned_uuid"
                    v-model:alias="form.warned_alias"
                    :initial-search="uuidParam"
                />
                <div v-if="form.errors.warned_uuid" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.warned_uuid }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="created_at" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Date of Warning<span class="text-red-500">*</span>
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
                <label for="warner_uuid" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Warned By</label>
                <PlayerPicker
                    v-model:uuid="form.warner_uuid"
                    v-model:alias="form.warner_alias"
                />
                <div v-if="form.errors.warner_uuid" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.warner_uuid }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Reason<span class="text-red-500">*</span>
                </label>
                <textarea
                    v-model="form.reason"
                    id="reason"
                    rows="3"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                ></textarea>
                <div v-if="form.errors.reason" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.reason }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Additional Information
                </label>
                <textarea
                    v-model="form.additional_info"
                    id="additional_info"
                    rows="8"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                ></textarea>
                <div v-if="form.errors.additional_info" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.additional_info }}
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
    </form>
</template>
