<script setup lang="ts">
import DateTimePicker from '../../../Components/DateTimePicker.vue'
import PlayerPicker from '../../../Components/PlayerPicker.vue'
import { useForm } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue'
import { Modal } from 'flowbite'
import { PlayerWarning } from '../../../Data/PlayerWarning'
import ErrorAlert from '../../../Components/ErrorAlert.vue'

interface Props {
    warning?: PlayerWarning,
    submit: Function,
}
const props = defineProps<Props>()

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

const deleteModal = ref()
const isEdit = computed(() => props.warning != null)

onMounted(() => {
    const $modalEl = document.getElementById('deleteModal');
    const options = {
        placement: 'bottom-right',
        backdrop: 'dynamic',
        backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40',
        closable: true,
    };
    deleteModal.value = new Modal($modalEl, options)
})

function submit() {
    props.submit(form)
}

function destroy() {
    deleteModal.value.hide()
    form.delete('/manage/warnings/' + props.warning.id)
}
</script>

<template>
    <form @submit.prevent="submit">
        <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4" />

        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
            <div class="col-span-2">
                <label for="warned_uuid" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Player<span class="text-red-500">*</span>
                </label>
                <PlayerPicker
                    v-model:uuid="form.warned_uuid"
                    v-model:alias="form.warned_alias"
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
                <label for="warner_uuid" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Banned By</label>
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

        <div class="flex flex-row gap-2">
            <button
                :disabled="form.processing"
                type="submit"
                class="px-5 py-2.5 mt-4 sm:mt-6 text-sm text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800"
            >
                {{ isEdit ? 'Update' : 'Create' }}
            </button>

            <button
                v-if="isEdit"
                type="button"
                class="
                    px-5 py-2.5 mt-4 sm:mt-6
                    text-sm text-center text-red-500 border border-red-500 rounded-lg
                    focus:ring-4 focus:ring-red-200
                    hover:bg-red-50
                    dark:focus:ring-red-900
                "
                @click="deleteModal.show"
            >
                Delete
            </button>
        </div>

        <div id="deleteModal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <div class="p-4 md:p-8 text-center">
                        <h3 class="mb-5 text-2xl font-bold">
                            Are you sure?
                        </h3>
                        <div class="text-sm mb-8 text-gray-500">
                            This cannot be undone
                        </div>
                        <button
                            type="button"
                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                            @click="destroy"
                        >
                            Yes, Delete It
                        </button>
                        <button
                            type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                            @click="deleteModal.hide"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
