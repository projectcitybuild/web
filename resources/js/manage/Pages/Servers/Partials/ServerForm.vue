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
    form.delete('/manage/servers/' + props.server.id)
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
            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-foreground">
                    Name<span class="text-danger">*</span>
                </label>
                <input
                    v-model="form.name"
                    id="name"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                >
                <div v-if="form.errors.name" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.name }}
                </div>
            </div>
            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-foreground">
                    IP Address<span class="text-danger">*</span>
                </label>
                <input
                    v-model="form.ip"
                    id="ip"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    placeholder="192.168.0.1"
                >
                <div v-if="form.errors.ip" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.ip }}
                </div>
            </div>
            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-foreground">
                    Port<span class="text-danger">*</span>
                </label>
                <input
                    v-model="form.port"
                    id="port"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    placeholder="25565"
                >
                <span class="block mt-2 text-xs font-medium text-subtle">
                    The Minecraft server port
                </span>
                <div v-if="form.errors.port" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.port }}
                </div>
            </div>
            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-foreground">
                    Web Port
                </label>
                <input
                    v-model="form.web_port"
                    id="web_port"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    placeholder="8080"
                >
                <span class="block mt-2 text-xs font-medium text-subtle">
                    The web port to send webhook events to
                </span>
                <div v-if="form.errors.web_port" class="text-xs text-danger font-bold mt-2">
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
