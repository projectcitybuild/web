<script setup lang="ts">
import { Routes } from '../../../Routes'
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import Button from '../../../shared/Components/Button.vue'
import LinkButton from '../../../shared/Components/LinkButton.vue'
import MailPreview from './Components/MailPreview.vue'

defineProps<AdminSharedPageProps & AdminInertiaPageProps['manage/PlayerMail/Create']>()

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
        <ManageFormPage title="Compose Announcement" description="Prepare a message for delivery to all Minecraft players.">
            <div class="grid gap-8 xl:grid-cols-2">
                    <form class="space-y-5" @submit.prevent="reviewAnnouncement">
                        <div>
                            <label for="subject" class="mb-2 block text-sm font-medium text-foreground">Subject <span class="text-danger">*</span></label>
                            <input
                                id="subject"
                                v-model="form.subject"
                                required
                                maxlength="191"
                                autocomplete="off"
                                :aria-invalid="form.errors.subject ? 'true' : undefined"
                                :aria-describedby="form.errors.subject ? 'subject-error' : undefined"
                                class="block w-full rounded-lg border border-border-strong bg-surface-muted p-2.5 text-sm text-foreground focus:border-primary focus:ring-focus"
                            >
                            <div class="mt-1 flex justify-between gap-2 text-xs text-muted">
                                <span v-if="form.errors.subject" id="subject-error" role="alert" class="font-medium text-danger">{{ form.errors.subject }}</span>
                                <span class="ml-auto">{{ form.subject.length }} / 191</span>
                            </div>
                        </div>

                        <div>
                            <label for="body" class="mb-2 block text-sm font-medium text-foreground">Message <span class="text-danger">*</span></label>
                            <textarea
                                id="body"
                                v-model="form.body"
                                required
                                maxlength="5000"
                                rows="9"
                                :aria-invalid="form.errors.body ? 'true' : undefined"
                                :aria-describedby="form.errors.body ? 'body-error' : undefined"
                                class="block w-full resize-y rounded-lg border border-border-strong bg-surface-muted p-2.5 text-sm text-foreground focus:border-primary focus:ring-focus"
                            />
                            <div class="mt-1 flex justify-between gap-2 text-xs text-muted">
                                <span v-if="form.errors.body" id="body-error" role="alert" class="font-medium text-danger">{{ form.errors.body }}</span>
                                <span class="ml-auto">{{ form.body.length }} / 5,000</span>
                            </div>
                        </div>

                        <div>
                            <span class="mb-2 block text-sm font-medium text-foreground">Audience</span>
                            <div class="border border-border bg-surface-muted px-4 py-3 text-sm text-muted dark:text-muted">
                                All Minecraft players <span class="text-muted">({{ recipientCount }} currently registered)</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <Button variant="primary" type="submit" :disabled="form.processing">
                                Review announcement
                            </Button>
                            <LinkButton :href="Routes.manage.playerMail.list" variant="ghost">Cancel</LinkButton>
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
                <p class="border-l-4 border-warning bg-warning-soft px-3 py-2 text-sm text-warning dark:text-warning">
                    This announcement cannot be recalled after delivery begins.
                </p>
            </div>
            <div class="flex flex-col-reverse justify-end gap-2 border-t border-border bg-surface-muted p-4 sm:flex-row">
                <Button variant="secondary" :disabled="form.processing" @click="confirmation?.close()">
                    Go back
                </Button>
                <Button variant="primary" :loading="form.processing" @click="sendAnnouncement">
                    {{ form.processing ? 'Sending...' : 'Confirm and send' }}
                </Button>
            </div>
        </dialog>
    </section>
</template>
