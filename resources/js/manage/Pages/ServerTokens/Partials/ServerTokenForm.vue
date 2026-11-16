<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { ServerToken } from '../../../Data/ServerToken'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import { Icons } from '../../../Icons'

interface Props {
    token?: ServerToken,
    submit: Function,
}

const props = defineProps<Props>()

const form = useForm<ServerToken>({
    description: props.token?.description,
    token: props.token?.token ?? generate(),
    allowed_ips: props.token?.allowed_ips?.split(',').join('\n'),
})

const deleteModal = ref<InstanceType<typeof ConfirmDialog>>()
const isEdit = computed(() => props.token != null)

function generate() {
    const length = 32
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
    let result = ''
    const randomArray = new Uint8Array(length)
    crypto.getRandomValues(randomArray)
    randomArray.forEach((number) => {
        result += chars[number % chars.length]
    })
    return result
}

function submit() {
    props.submit(form)
}

function destroy() {
    deleteModal?.value.close()
    form.delete('/manage/server-tokens/' + props.token.id)
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
                    Description<span class="text-danger">*</span>
                </label>
                <input
                    v-model="form.description"
                    id="name"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    placeholder="For PCBridge"
                >
                <span class="block mt-2 text-xs font-medium text-subtle">
                    Write a description that notes what is consuming the token (eg. PCBridge)
                </span>
                <div v-if="form.errors.description" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.description }}
                </div>
            </div>
            <div>
                <label for="description" class="block mb-2 text-sm font-medium text-foreground">
                    Access Token<span class="text-danger">*</span>
                </label>
                <div class="flex flex-row gap-2">
                    <input
                        v-model="form.token"
                        id="token"
                        class="grow p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    >
                    <FilledButton
                        variant="danger"
                        @click="form.token = generate()"
                    >
                        <SvgIcon :svg="Icons.refresh" />
                    </FilledButton>
                </div>
                <div v-if="form.errors.token" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.token }}
                </div>
            </div>
            <div class="sm:col-span-2">
                <label for="allowed_ips" class="block mb-2 text-sm font-medium text-foreground">
                    Allowed IPs
                </label>
                <textarea
                    v-model="form.allowed_ips"
                    id="allowed_ips"
                    rows="4"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                ></textarea>
                <span class="block mt-2 text-xs font-medium text-subtle">
                    IP addresses permitted to use this token to hit the API - <strong>one per line</strong>.<br />
                    Leave blank if any IP address allowed.
                </span>
                <div v-if="form.errors.allowed_ips" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.allowed_ips }}
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
                <SvgIcon :svg="Icons.bin" />
                Delete
            </OutlinedButton>
        </div>
    </form>
</template>
