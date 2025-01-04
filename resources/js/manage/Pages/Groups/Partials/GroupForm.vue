<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import type { Group } from '../../../Data/Group'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'

interface Props {
    group?: Group,
    submit: Function,
}

const props = defineProps<Props>()

const form = useForm<Group>({
    name: props.group?.name,
    alias: props.group?.alias,
    minecraft_name: props.group?.minecraft_name,
    minecraft_display_name: props.group?.minecraft_display_name,
    minecraft_hover_text: props.group?.minecraft_hover_text,
    is_admin: props.group?.is_admin,
    is_default: props.group?.is_default,
    group_type: props.group?.group_type ?? 'build',
    display_priority: props.group?.priority,
    created_at: props.badge?.created_at
        ? new Date(props.badge.created_at)
        : new Date(),
})

const deleteModal = ref<InstanceType<typeof ConfirmDialog>>()
const isEdit = computed(() => props.group != null)

function submit() {
    props.submit(form)
}

function destroy() {
    deleteModal?.value.close()
    form.delete('/manage/groups/' + props.group.group_id)
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
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Name<span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.name"
                    id="name"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                <div v-if="form.errors.name" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.name }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="alias" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Alias
                </label>
                <input
                    v-model="form.alias"
                    id="alias"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                <div v-if="form.errors.alias" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.alias }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="group_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Group Type<span class="text-red-500">*</span>
                </label>
                <select
                    v-model="form.group_type"
                    id="group_type"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                >
                    <option value="build">Build</option>
                    <option value="trust">Trust</option>
                    <option value="donor">Donor</option>
                    <option value="staff">Staff</option>
                </select>
                <div v-if="form.errors.group_type" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.group_type }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="minecraft_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Minecraft Name
                </label>
                <input
                    v-model="form.minecraft_name"
                    id="minecraft_name"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    Name of the Minecraft group in the Permission system (eg. in <i>LuckPerms</i>) that this group is linked to
                </span>
                <div v-if="form.errors.minecraft_name" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.minecraft_name }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="minecraft_display_name"
                       class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Minecraft Display Name
                </label>
                <input
                    v-model="form.minecraft_display_name"
                    id="minecraft_display_name"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="<blue>[A]</blue>"
                >
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    The text displayed to represent this group in chat. Supports <a class="text-blue-500"
                                                                                    href="https://docs.advntr.dev/minimessage/format.html"
                                                                                    target="_blank">MiniMessage</a> format
                </span>
                <div v-if="form.errors.minecraft_display_name" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.minecraft_display_name }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="minecraft_hover_text" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Minecraft Hover Text
                </label>
                <input
                    v-model="form.minecraft_hover_text"
                    id="minecraft_hover_text"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Architect"
                >
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    The text displayed when the Minecraft Display Name is hovered-over. Supports <a
                    class="text-blue-500" href="https://docs.advntr.dev/minimessage/format.html" target="_blank">MiniMessage</a> format
                </span>
                <div v-if="form.errors.minecraft_hover_text" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.minecraft_hover_text }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="display_priority" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Display Priority
                </label>
                <input
                    v-model="form.display_priority"
                    type="number"
                    id="display_priority"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="1"
                >
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    If a player belongs to multiple groups of the same Group Type, the group with the <strong>highest</strong> Display Priority will be shown in chat.
                    <br/><br/>
                    Do not assign the same priority as another group in the same Group Type.
                </span>
                <div v-if="form.errors.display_priority" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.display_priority }}
                </div>
            </div>
        </div>

        <div class="flex flex-row gap-2 mt-8">
            <FilledButton
                variant="primary"
                :disabled="form.processing"
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
