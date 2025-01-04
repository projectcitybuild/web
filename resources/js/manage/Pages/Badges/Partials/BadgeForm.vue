<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue'
import type { Badge } from '../../../Data/Badge'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'

interface Props {
    badge?: Badge,
    submit: Function,
}

const props = defineProps<Props>()

const form = useForm<Badge>({
    display_name: props.badge?.display_name,
    unicode_icon: props.badge?.unicode_icon,
    list_hidden: props.badge?.list_hidden ? 1 : 0,
    created_at: props.badge?.created_at
        ? new Date(props.badge.created_at)
        : new Date(),
})

const deleteModal = ref<InstanceType<typeof ConfirmDialog>>()
const isEdit = computed(() => props.badge != null)

function submit() {
    props.submit(form)
}

function destroy() {
    deleteModal?.value.close()
    form.delete('/manage/badges/' + props.badge.id)
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
                <label for="display_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Name<span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.display_name"
                    id="display_name"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="eg. Build-Off Winner"
                >
                <div v-if="form.errors.display_name" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.display_name }}
                </div>
            </div>
            <div>
                <label for="unicode_icon" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Unicode Icon<span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.unicode_icon"
                    id="unicode_icon"
                    placeholder="<gold>★</gold>"
                    class="block p-2.5 text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    Character/s to use as the badge icon in chat. Supports <a class="text-blue-500"
                                                                              href="https://docs.advntr.dev/minimessage/format.html"
                                                                              target="_blank">MiniMessage</a> format
                </span>
                <div v-if="form.errors.unicode_icon" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.unicode_icon }}
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <input
                        v-model="form.list_hidden"
                        true-value="1"
                        false-value="0"
                        id="list_hidden"
                        type="checkbox"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    >
                    <label for="list_hidden" class="ms-2 text-sm text-gray-900 dark:text-gray-300">
                        Hidden in List
                    </label>
                </div>
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    Whether this badge will appear in the "attainable badges" list
                </span>
                <div v-if="form.errors.list_hidden" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.list_hidden }}
                </div>
            </div>
        </div>

        <div class="flex flex-row gap-2">
            <FilledButton
                variant="primary"
                :disabled="form.processing"
                type="submit"
                class="mt-8"
            >
                <Spinner v-if="form.processing" />
                <template v-else>{{ isEdit ? 'Update' : 'Create' }}</template>
            </FilledButton>

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
                @click="deleteModal.open"
            >
                Delete
            </button>
        </div>
    </form>
</template>
