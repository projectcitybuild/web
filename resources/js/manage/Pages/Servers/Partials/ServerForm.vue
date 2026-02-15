<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { Server } from '../../../Data/Server'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import { Icons } from '../../../Icons'

interface Props {
    server?: Server,
    submit: Function,
}

const props = defineProps<Props>()

const form = useForm<Server>({
    name: props.server?.name,
    ip: props.server?.ip,
    port: props.server?.port,
    web_port: props.server?.web_port,
})

const deleteModal = ref<InstanceType<typeof ConfirmDialog>>()
const isEdit = computed(() => props.server != null)

function submit() {
    props.submit(form)
}

function destroy() {
    deleteModal?.value.close()
    form.delete('/manage/servers/' + props.server.server_id)
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
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
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
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    IP Address<span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.ip"
                    id="ip"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="192.168.0.1"
                >
                <div v-if="form.errors.ip" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.ip }}
                </div>
            </div>
            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Port<span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.port"
                    id="port"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="25565"
                >
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    The Minecraft server port
                </span>
                <div v-if="form.errors.port" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.port }}
                </div>
            </div>
            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Web Port
                </label>
                <input
                    v-model="form.web_port"
                    id="web_port"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="8080"
                >
                <span class="block mt-2 text-xs font-medium text-gray-400 dark:text-white">
                    The web port to send webhook events to
                </span>
                <div v-if="form.errors.web_port" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.web_port }}
                </div>
            </div>
        </div>

        <div class="flex flex-row gap-2 mt-8">
            <FilledButton
                variant="primary"
                :disabled="form.processing"
                @click="submit"
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
                <SvgIcon :svg="Icons.bin" />
                Delete
            </OutlinedButton>
        </div>
    </form>
</template>
