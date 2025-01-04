<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue'
import type { Donation } from '../../../Data/Donation'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import DateTimePicker from '../../../Components/DateTimePicker.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'

interface Props {
    donation?: Donation,
    submit: Function,
}

const props = defineProps<Props>()

const form = useForm<Donation>({
    amount: props.donation?.amount ?? 1,
    created_at: props.donation?.created_at
        ? new Date(props.donation.created_at)
        : new Date(),
})

const deleteModal = ref<InstanceType<typeof ConfirmDialog>>()
const isEdit = computed(() => props.donation != null)

function submit() {
    props.submit(form)
}

function destroy() {
    deleteModal?.value.close()
    form.delete('/manage/donations/' + props.donation.donation_id)
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
                <label for="amount" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Amount in dollars (AUD)<span class="text-red-500">*</span>
                </label>
                <input
                    v-model="form.amount"
                    type="number"
                    id="amount"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                >
                <div v-if="form.errors.amount" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.amount }}
                </div>
            </div>
            <div class="col-span-2">
                <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Date<span class="text-red-500">*</span>
                </label>
                <DateTimePicker
                    v-model="form.created_at"
                    @change="form.created_at = $event"
                />
                <div v-if="form.errors.created_at" class="text-xs text-red-500 font-bold mt-2">
                    {{ form.errors.created_at }}
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

            <OutlinedButton
                variant="danger"
                v-if="isEdit"
                type="button"
                @click="deleteModal.open()"
            >
                <SvgIcon icon="bin" />
                Delete
            </OutlinedButton>
        </div>
    </form>
</template>
