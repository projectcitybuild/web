<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import DateTimePicker from '../../../Components/DateTimePicker.vue'
import type { Player } from '../../../Data/Player'
import PlayerPicker from '../../../Components/PlayerPicker.vue'
import Button from '../../../../shared/Components/Button.vue'
import Input from '../../../../shared/Components/Input.vue'

interface Props {
    player?: Player,
    submit: Function,
}

const props = defineProps<Props>()

const form = useForm<Player>({
    uuid: props.player?.uuid,
    alias: props.player?.alias,
    nickname: props.player?.nickname,
    account_id: props.player?.account_id,
    created_at: props.player?.created_at
        ? new Date(props.player.created_at)
        : new Date(),
})

const isEdit = computed(() => props.player != null)

function submit() {
    props.submit(form)
}
</script>

<template>
    <form @submit.prevent="submit">
        <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

        <div class="grid gap-4 sm:grid-cols-2 sm:gap-6">
            <div class="sm:col-span-2">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Player<span class="text-red-500">*</span>
                    </label>
                    <PlayerPicker
                        v-model:uuid="form.uuid"
                        v-model:alias="form.alias"
                    />
                </div>
                <div class="text-gray-500 text-sm mt-4">or</div>
            </div>
            <div>
                <Input
                    v-model="form.uuid"
                    id="uuid"
                    label="UUID"
                    :error="form.errors.uuid"
                    required
                />
            </div>
            <div>
                <Input
                    v-model="form.alias"
                    id="alias"
                    label="Last Known Alias"
                    :error="form.errors.alias"
                    required
                />
            </div>
            <div>
                <Input
                    v-model="form.nickname"
                    id="nickname"
                    label="In-Game Nickname"
                    :error="form.errors.nickname"
                />
            </div>

            <hr class="sm:col-span-2" />

            <div>
                <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    First Seen<span class="text-red-500">*</span>
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

        <Button
            type="submit"
            variant="primary"
            :loading="form.processing"
            class="mt-8"
        >
            {{ isEdit ? 'Update' : 'Create' }}
        </Button>
    </form>
</template>
