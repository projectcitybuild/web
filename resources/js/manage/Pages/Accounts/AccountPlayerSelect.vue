<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import ManageFormPage from '../../Components/ManageFormPage.vue'
import type { Account } from '../../Data/Account'
import ErrorAlert from '../../Components/ErrorAlert.vue'
import FilledButton from '../../Components/FilledButton.vue'
import Spinner from '../../Components/Spinner.vue'
import PlayerPicker from '../../Components/PlayerPicker.vue'

interface Props {
    account_id: number,
}
const props = defineProps<Props>()

const form = useForm({
    uuid: null,
    alias: null,
})

function submit() {
    form.post('/manage/accounts/' + props.account_id + '/players')
}
</script>

<template>
    <ManageFormPage title="Link a Player" description="Associates a Minecraft player with this account">
        <form @submit.prevent="submit">
            <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4" />

            <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
                <div class="col-span-2">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Player<span class="text-red-500">*</span>
                    </label>
                    <PlayerPicker
                        v-model:uuid="form.uuid"
                        v-model:alias="form.alias"
                    />
                </div>
            </div>

            <FilledButton
                variant="primary"
                :disabled="form.processing"
                type="submit"
                class="mt-8"
            >
                <Spinner v-if="form.processing" />
                <template v-else>Add</template>
            </FilledButton>
        </form>
    </ManageFormPage>
</template>
