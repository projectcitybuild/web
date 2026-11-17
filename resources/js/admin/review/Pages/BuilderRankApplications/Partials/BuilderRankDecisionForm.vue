<script setup lang="ts">
import Card from '../../../../shared/Components/Card.vue'
import { useForm } from '@inertiajs/vue3'
import { BuilderRankApplication } from '../../../../manage/Data/BuilderRankApplication'
import ErrorAlert from '../../../../manage/Components/ErrorAlert.vue'
import ConfirmDialog from '../../../../manage/Components/ConfirmDialog.vue'
import { computed, ref } from 'vue'
import { Role } from '../../../../manage/Data/Role'
import Button from '../../../../shared/Components/Button.vue'
import Select from '../../../../shared/Components/Select.vue'
import Textarea from '../../../../shared/Components/Textarea.vue'

interface Props {
    application: BuilderRankApplication,
    buildRoles: Role[],
}
const props = defineProps<Props>()

const form = useForm({
    status: null,
    promote_role: null,
    deny_reason: null,
})

enum Decision {
    approve,
    deny,
}
const decision = ref<Decision|null>(null)
const confirmationDialog = ref<InstanceType<typeof ConfirmDialog> | null>(null)
const confirmationMessage = computed(() => {
    const action = decision.value === Decision.approve ? 'approve' : 'deny'
    return `You are about to ${action} Builder Rank Application #${props.application.id}. This decision cannot be reversed.`
})

function approve() {
    form.post('/review/builder-ranks/' + props.application.id + '/approve')
}

function deny() {
    form.post('/review/builder-ranks/' + props.application.id + '/deny')
}

function requestConfirmation() {
    if (!form.processing) {
        confirmationDialog.value?.open()
    }
}

function confirmDecision() {
    if (form.processing || decision.value === null) {
        return
    }

    confirmationDialog.value?.close()

    if (decision.value === Decision.approve) {
        approve()
        return
    }

    deny()
}
</script>

<template>
    <div>
        <Card padding="none" class="overflow-hidden rounded-xl border border-border bg-surface">
            <div class="p-4">
                <h2 class="text-base font-semibold">Review panel</h2>
                <p class="mt-1 text-sm text-muted">Choose an outcome for this application.</p>
            </div>

            <fieldset class="flex flex-col gap-2 border-t border-border p-4">
                <legend class="sr-only">Council decision</legend>
                <label for="approve" class="flex items-center gap-2 text-sm font-medium text-foreground">
                    <input
                        id="approve"
                        type="radio"
                        name="decision"
                        v-model="decision"
                        :value="Decision.approve"
                        class="size-4 accent-primary focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                    />
                    Approve
                </label>
                <label for="deny" class="flex items-center gap-2 text-sm font-medium text-foreground">
                    <input
                        id="deny"
                        type="radio"
                        name="decision"
                        v-model="decision"
                        :value="Decision.deny"
                        class="size-4 accent-primary focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                    />
                    Deny
                </label>

                <div class="mt-2 text-sm text-muted">
                    <strong>Applications cannot be re-opened after submitting this form.</strong><br />
                    Please finalize the decision with other council members before proceeding.
                </div>
            </fieldset>

            <form @submit.prevent class="border-t border-border p-4" v-if="decision != null">
                <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

                <template v-if="decision === Decision.approve">
                    <div class="space-y-6">
                        <div class="mb-4">
                            <Select
                                v-model.number="form.promote_role"
                                id="activated"
                                label="Promote to..."
                                placeholder="Select a build role..."
                                :error="form.errors.promote_role"
                            >
                                <option v-for="role in buildRoles" :value="role.id">{{ role.name }}</option>
                            </Select>
                        </div>
                    </div>

                    <Button
                        variant="primary"
                        @click="requestConfirmation"
                        :loading="form.processing"
                        class="mt-8"
                    >
                        Promote and Close
                    </Button>
                </template>

                <template v-if="decision === Decision.deny">
                    <div>
                        <Textarea
                            v-model="form.deny_reason"
                            id="deny_reason"
                            rows="10"
                            label="Denial Reason & Feedback"
                            description="This message is emailed to the player. Include the reasoning for the decision and, if possible, feedback on their build."
                            :error="form.errors.deny_reason"
                        />
                    </div>

                    <Button
                        variant="danger"
                        @click="requestConfirmation"
                        :loading="form.processing"
                        class="mt-8"
                    >
                        Deny and Close
                    </Button>
                </template>
            </form>
        </Card>

        <ConfirmDialog
            ref="confirmationDialog"
            :title="decision === Decision.approve ? 'Approve Application?' : 'Deny Application?'"
            :message="confirmationMessage"
            :confirm-title="decision === Decision.approve ? 'Promote and Close' : 'Deny and Close'"
            :confirm-variant="decision === Decision.approve ? 'primary' : 'danger'"
            :on-confirm="confirmDecision"
        />
    </div>
</template>
