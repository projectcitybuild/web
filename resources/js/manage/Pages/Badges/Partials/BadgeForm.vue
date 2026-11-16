<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue'
import type { Badge } from '../../../Data/Badge'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import { Icons } from '../../../Icons'

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
            <div class="sm:col-span-2">
                <label for="display_name" class="block mb-2 text-sm font-medium text-foreground">
                    Name<span class="text-danger">*</span>
                </label>
                <input
                    v-model="form.display_name"
                    id="display_name"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    placeholder="eg. Build-Off Winner"
                >
                <div v-if="form.errors.display_name" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.display_name }}
                </div>
            </div>
            <div>
                <label for="unicode_icon" class="block mb-2 text-sm font-medium text-foreground">
                    Unicode Icon<span class="text-danger">*</span>
                </label>
                <input
                    v-model="form.unicode_icon"
                    id="unicode_icon"
                    placeholder="<gold>★</gold>"
                    class="block p-2.5 text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                >
                <span class="block mt-2 text-xs font-medium text-subtle">
                    Character/s to use as the badge icon in chat. Supports <a class="text-primary"
                                                                              href="https://docs.advntr.dev/minimessage/format.html"
                                                                              target="_blank">MiniMessage</a> format
                </span>
                <div v-if="form.errors.unicode_icon" class="text-xs text-danger font-bold mt-2">
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
                        class="w-4 h-4 text-primary bg-surface-muted border-border-strong rounded focus:ring-focus ring-offset-surface focus:ring-2"
                    >
                    <label for="list_hidden" class="ms-2 text-sm text-foreground dark:text-subtle">
                        Hidden in List
                    </label>
                </div>
                <span class="block mt-2 text-xs font-medium text-subtle">
                    Whether this badge will appear in the "attainable badges" list
                </span>
                <div v-if="form.errors.list_hidden" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.list_hidden }}
                </div>
            </div>
        </div>

        <div class="flex flex-row gap-2 mt-8">
            <FilledButton
                variant="primary"
                :disabled="form.processing"
                type="submit"
            >
                <Spinner v-if="form.processing" />
                <template v-else>{{ isEdit ? 'Update' : 'Create' }}</template>
            </FilledButton>

            <OutlinedButton
                variant="danger"
                v-if="isEdit"
                @click="deleteModal.open()"
            >
                <SvgIcon :svg="Icons.bin" />
                Delete
            </OutlinedButton>
        </div>
    </form>
</template>
