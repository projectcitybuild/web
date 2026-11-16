<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, onMounted, ref } from 'vue'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import { Icons } from '../../../Icons'
import { Home } from '../../../Data/Home'
import PlayerPicker from '../../../Components/PlayerPicker.vue'

interface Props {
    home?: Home,
    submit: Function,
}

const props = defineProps<Props>()
const form = useForm<Home>({
    player_uuid: props.home?.player?.uuid,
    player_alias: props.home?.player?.alias,
    name: props.home?.name,
    world: props.home?.world,
    x: props.home?.x,
    y: props.home?.y,
    z: props.home?.z,
    pitch: props.home?.pitch,
    yaw: props.home?.yaw,
    created_at: props.home?.created_at
        ? new Date(props.home.created_at)
        : new Date(),
})

const deleteModal = ref<InstanceType<typeof ConfirmDialog>>()
const isEdit = computed(() => props.home != null)

function submit() {
    props.submit(form)
}

function destroy() {
    deleteModal?.value.close()
    form.delete('/manage/homes/' + props.home.id)
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
                <label for="name" class="block mb-2 text-sm font-medium text-foreground">
                    Player<span class="text-danger">*</span>
                </label>
                <PlayerPicker
                    v-model:uuid="form.player_uuid"
                    v-model:alias="form.player_alias"
                />
                <div v-if="form.errors.player_id" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.player_id }}
                </div>
            </div>
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-foreground">
                    Name<span class="text-danger">*</span>
                </label>
                <input
                    v-model="form.name"
                    id="ip"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                >
                <span class="block mt-2 text-xs font-medium text-subtle">
                    Case-sensitive and cannot contain white
                </span>
                <div v-if="form.errors.name" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.name }}
                </div>
            </div>
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-foreground">
                    World Name<span class="text-danger">*</span>
                </label>
                <input
                    v-model="form.world"
                    id="world"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                >
                <span class="block mt-2 text-xs font-medium text-subtle">
                    Warning: Case-sensitive
                </span>
                <div v-if="form.errors.world" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.world }}
                </div>
            </div>
            <div class="sm:col-span-2 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-foreground">
                        X<span class="text-danger">*</span>
                    </label>
                    <input
                        v-model="form.x"
                        type="number"
                        id="x"
                        class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    >
                    <div v-if="form.errors.x" class="text-xs text-danger font-bold mt-2">
                        {{ form.errors.x }}
                    </div>
                </div>
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-foreground">
                        Y<span class="text-danger">*</span>
                    </label>
                    <input
                        v-model="form.y"
                        type="number"
                        id="y"
                        class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    >
                    <div v-if="form.errors.y" class="text-xs text-danger font-bold mt-2">
                        {{ form.errors.y }}
                    </div>
                </div>
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-foreground">
                        Z<span class="text-danger">*</span>
                    </label>
                    <input
                        v-model="form.z"
                        type="number"
                        id="z"
                        class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    >
                    <div v-if="form.errors.z" class="text-xs text-danger font-bold mt-2">
                        {{ form.errors.z }}
                    </div>
                </div>
            </div>
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-foreground">
                    Pitch<span class="text-danger">*</span>
                </label>
                <input
                    v-model="form.pitch"
                    type="number"
                    id="pitch"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                >
                <div v-if="form.errors.pitch" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.pitch }}
                </div>
            </div>
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-foreground">
                    Yaw<span class="text-danger">*</span>
                </label>
                <input
                    v-model="form.yaw"
                    type="number"
                    id="yaw"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                >
                <div v-if="form.errors.yaw" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.yaw }}
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
