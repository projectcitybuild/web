<script setup lang="ts">
import { Head, InertiaForm, useForm } from '@inertiajs/vue3'
import Card from '../../Components/Card.vue'
import BackButton from '../../Components/BackButton.vue'
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
    <section>
        <Head title="Link a Player"/>

        <Card>
            <div class="p-8 max-w-2xl">
                <BackButton :href="'/manage/accounts/' + props.account_id" class="mb-4"/>

                <h2 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Link a Player</h2>
                <div class="text-sm text-gray-500">Associates a Minecraft player with this account</div>
                <hr class="my-6"/>

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
            </div>
        </Card>
    </section>
</template>
