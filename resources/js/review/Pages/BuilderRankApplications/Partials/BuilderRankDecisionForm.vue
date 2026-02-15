<script setup lang="ts">
import Card from '../../../../manage/Components/Card.vue'
import { useForm } from '@inertiajs/vue3'
import { BuilderRankApplication } from '../../../../manage/Data/BuilderRankApplication'
import FilledButton from '../../../../manage/Components/FilledButton.vue'
import ErrorAlert from '../../../../manage/Components/ErrorAlert.vue'
import { ref } from 'vue'
import { Role } from '../../../../manage/Data/Role'

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

function approve() {
    form.post('/review/builder-ranks/' + props.application.id + '/approve')
}

function deny() {
    form.post('/review/builder-ranks/' + props.application.id + '/deny')
}
</script>

<template>
    <div>
        <Card>
            <div class="p-4">
                <h2 class="font-bold">Council Decision</h2>
            </div>

            <div class="p-4 border-t border-gray-200 flex flex-col gap-2">
                <label for="approve">
                    <input
                        id="approve"
                        type="radio"
                        name="decision"
                        v-model="decision"
                        :value="Decision.approve"
                        class="mr-1"
                    />
                    Approve
                </label>
                <label for="deny">
                    <input
                        id="deny"
                        type="radio"
                        name="decision"
                        v-model="decision"
                        :value="Decision.deny"
                        class="mr-1"
                    />
                    Deny
                </label>

                <div class="mt-4 text-sm">
                    <strong>Applications cannot be re-opened after submitting this form.</strong><br />
                    Please finalize the decision with other council members before proceeding.
                </div>
            </div>

            <form @submit.prevent class="p-4 border-t border-gray-200" v-if="decision != null">
                <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

                <template v-if="decision === Decision.approve">
                    <div class="space-y-6">
                        <div class="mb-4">
                            <label for="activated" class="text-xs text-gray-700 font-bold">Promote to...</label>
                            <select
                                v-model="form.promote_role"
                                id="activated"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                                <option :value="null">Select a build role...</option>
                                <option v-for="role in buildRoles" :value="role.id">{{ role.name }}</option>
                            </select>
                        </div>
                    </div>

                    <FilledButton
                        variant="danger"
                        @click="approve"
                        :disabled="form.processing"
                        class="mt-8"
                    >
                        Promote and Close
                    </FilledButton>
                </template>

                <template v-if="decision === Decision.deny">
                    <div>
                        <label for="deny_reason" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Denial Reason & Feedback
                        </label>
                        <textarea
                            v-model="form.deny_reason"
                            id="deny_reason"
                            rows="10"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        ></textarea>
                        <span class="block mt-2 text-xs text-gray-400">
                            This is the message emailed to the player regarding their application.<br />
                            <strong>Please include the reasoning for the decision, and if possible, feedback on their build</strong>
                        </span>
                    </div>

                    <FilledButton
                        variant="danger"
                        @click="deny"
                        :disabled="form.processing"
                        class="mt-8"
                    >
                        Deny and Close
                    </FilledButton>
                </template>
            </form>
        </Card>
    </div>
</template>
