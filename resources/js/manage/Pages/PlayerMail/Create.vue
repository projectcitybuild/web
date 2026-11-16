<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import Spinner from '../../Components/Spinner.vue'
import FilledButton from '../../Components/FilledButton.vue'
import MailPreview from './Components/MailPreview.vue'

interface Props {
    recipientCount: number,
    sender: string,
}

defineProps<Props>()

const form = useForm({
    subject: '',
    body: '',
})

const confirmation = ref<HTMLDialogElement>()

function reviewAnnouncement() {
    if (!form.processing) {
        confirmation.value?.showModal()
    }
}

function sendAnnouncement() {
    if (form.processing) {
        return
    }

    form.post('/manage/player-mail', {
        onError: () => confirmation.value?.close(),
    })
}
</script>

<template>
    <section>
        <ManageFormPage title="Compose announcement" description="This message will be sent to all Minecraft players.">
            <div class="grid gap-8 xl:grid-cols-2">
                    <form class="space-y-5" @submit.prevent="reviewAnnouncement">
                        <div>
                            <label for="subject" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Subject <span class="text-red-600">*</span></label>
                            <input
                                id="subject"
                                v-model="form.subject"
                                required
                                maxlength="191"
                                autocomplete="off"
                                :aria-invalid="form.errors.subject ? 'true' : undefined"
                                :aria-describedby="form.errors.subject ? 'subject-error' : undefined"
                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                            <div class="mt-1 flex justify-between gap-2 text-xs text-gray-500">
                                <span v-if="form.errors.subject" id="subject-error" role="alert" class="font-medium text-red-600">{{ form.errors.subject }}</span>
                                <span class="ml-auto">{{ form.subject.length }} / 191</span>
                            </div>
                        </div>

                        <div>
                            <label for="body" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Message <span class="text-red-600">*</span></label>
                            <textarea
                                id="body"
                                v-model="form.body"
                                required
                                maxlength="5000"
                                rows="9"
                                :aria-invalid="form.errors.body ? 'true' : undefined"
                                :aria-describedby="form.errors.body ? 'body-error' : undefined"
                                class="block w-full resize-y rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                            <div class="mt-1 flex justify-between gap-2 text-xs text-gray-500">
                                <span v-if="form.errors.body" id="body-error" role="alert" class="font-medium text-red-600">{{ form.errors.body }}</span>
                                <span class="ml-auto">{{ form.body.length }} / 5,000</span>
                            </div>
                        </div>

                        <div>
                            <span class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Audience</span>
                            <div class="border border-border bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                All Minecraft players <span class="text-gray-500">({{ recipientCount }} currently registered)</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <FilledButton variant="primary" type="submit" :disabled="form.processing">
                                Review announcement
                            </FilledButton>
                            <Link href="/manage/player-mail" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">Cancel</Link>
                        </div>
                    </form>

                    <div class="space-y-3">
                        <h2 class="text-base font-semibold text-foreground">Player preview</h2>
                        <MailPreview :subject="form.subject" :body="form.body" />
                    </div>
            </div>
        </ManageFormPage>

        <dialog
            ref="confirmation"
            aria-labelledby="confirmation-title"
            aria-describedby="confirmation-description"
            class="m-auto max-h-[calc(100dvh-2rem)] w-[min(42rem,calc(100%-2rem))] overflow-y-auto border border-border bg-surface p-0 text-foreground shadow-xl backdrop:bg-black/50"
        >
            <div class="space-y-5 p-5 sm:p-7">
                <div>
                    <h2 id="confirmation-title" class="text-lg font-semibold">Confirm announcement</h2>
                    <p id="confirmation-description" class="mt-1 text-sm text-muted">Review the audience and message before sending.</p>
                </div>
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-muted">Audience</dt>
                        <dd class="font-medium">All Minecraft players (about {{ recipientCount }})</dd>
                    </div>
                    <div>
                        <dt class="text-muted">Sending as</dt>
                        <dd class="font-medium">{{ sender }}</dd>
                    </div>
                </dl>
                <MailPreview :subject="form.subject" :body="form.body" />
                <p class="border-l-4 border-amber-500 bg-amber-50 px-3 py-2 text-sm text-amber-900 dark:bg-amber-950 dark:text-amber-100">
                    This announcement cannot be recalled after delivery begins.
                </p>
            </div>
            <div class="flex flex-col-reverse justify-end gap-2 border-t border-border bg-surface-muted p-4 sm:flex-row">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:text-gray-200 dark:hover:bg-gray-700" :disabled="form.processing" @click="confirmation?.close()">
                    Go back
                </button>
                <button type="button" class="inline-flex items-center justify-center gap-2 bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-60" :disabled="form.processing" @click="sendAnnouncement">
                    <Spinner v-if="form.processing" />
                    {{ form.processing ? 'Sending...' : 'Confirm and send' }}
                </button>
            </div>
        </dialog>
    </section>
</template>
