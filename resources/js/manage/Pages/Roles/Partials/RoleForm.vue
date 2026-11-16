<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import type { Role } from '../../../Data/Role'
import ErrorAlert from '../../../Components/ErrorAlert.vue'
import FilledButton from '../../../Components/FilledButton.vue'
import Spinner from '../../../Components/Spinner.vue'
import ConfirmDialog from '../../../Components/ConfirmDialog.vue'
import SvgIcon from '../../../Components/SvgIcon.vue'
import OutlinedButton from '../../../Components/OutlinedButton.vue'
import { Icons } from '../../../Icons'

interface Props {
    role?: Role,
    submit: Function,
}

const props = defineProps<Props>()

const form = useForm<Role>({
    name: props.role?.name,
    alias: props.role?.alias,
    minecraft_name: props.role?.minecraft_name,
    minecraft_display_name: props.role?.minecraft_display_name,
    minecraft_hover_text: props.role?.minecraft_hover_text,
    is_admin: props.role?.is_admin,
    is_default: props.role?.is_default,
    role_type: props.role?.role_type ?? 'build',
    display_priority: props.role?.priority,
    additional_homes: props.role?.additional_homes ?? 0,
    created_at: props.role?.created_at
        ? new Date(props.role.created_at)
        : new Date(),
})

const deleteModal = ref<InstanceType<typeof ConfirmDialog>>()
const isEdit = computed(() => props.role != null)

function submit() {
    props.submit(form)
}

function destroy() {
    deleteModal?.value.close()
    form.delete('/manage/roles/' + props.role.id)
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
            <div>
                <label for="name" class="block mb-2 text-sm font-medium text-foreground">
                    Name<span class="text-danger">*</span>
                </label>
                <input
                    v-model="form.name"
                    id="name"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                >
                <div v-if="form.errors.name" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.name }}
                </div>
            </div>
            <div>
                <label for="alias" class="block mb-2 text-sm font-medium text-foreground">
                    Alias
                </label>
                <input
                    v-model="form.alias"
                    id="alias"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                >
                <div v-if="form.errors.alias" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.alias }}
                </div>
            </div>
            <div>
                <label for="role_type" class="block mb-2 text-sm font-medium text-foreground">
                    Role Type<span class="text-danger">*</span>
                </label>
                <select
                    v-model="form.role_type"
                    id="role_type"
                    class="bg-surface-muted border border-border-strong text-foreground text-sm rounded-lg focus:ring-focus focus:border-primary block w-full p-2.5 placeholder:text-subtle"
                >
                    <option value="build">Build</option>
                    <option value="trust">Trust</option>
                    <option value="donor">Donor</option>
                    <option value="staff">Staff</option>
                </select>
                <div v-if="form.errors.role_type" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.role_type }}
                </div>
            </div>
            <div>
                <label for="minecraft_name" class="block mb-2 text-sm font-medium text-foreground">
                    Minecraft Name
                </label>
                <input
                    v-model="form.minecraft_name"
                    id="minecraft_name"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                >
                <span class="block mt-2 text-xs font-medium text-subtle">
                    Name of the Minecraft group in the Permission system (eg. in <i>LuckPerms</i>) that this role is linked to
                </span>
                <div v-if="form.errors.minecraft_name" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.minecraft_name }}
                </div>
            </div>
            <div class="sm:col-span-2">
                <label for="minecraft_display_name"
                       class="block mb-2 text-sm font-medium text-foreground">
                    Minecraft Display Name
                </label>
                <input
                    v-model="form.minecraft_display_name"
                    id="minecraft_display_name"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    placeholder="<blue>[A]</blue>"
                >
                <span class="block mt-2 text-xs font-medium text-subtle">
                    The text displayed to represent this role in chat. Supports <a class="text-primary" href="https://docs.advntr.dev/minimessage/format.html" target="_blank">MiniMessage</a> format
                </span>
                <div v-if="form.errors.minecraft_display_name" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.minecraft_display_name }}
                </div>
            </div>
            <div class="sm:col-span-2">
                <label for="minecraft_hover_text" class="block mb-2 text-sm font-medium text-foreground">
                    Minecraft Hover Text
                </label>
                <input
                    v-model="form.minecraft_hover_text"
                    id="minecraft_hover_text"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    placeholder="Architect"
                >
                <span class="block mt-2 text-xs font-medium text-subtle">
                    The text displayed when the Minecraft Display Name is hovered-over. Supports <a
                    class="text-primary" href="https://docs.advntr.dev/minimessage/format.html" target="_blank">MiniMessage</a> format
                </span>
                <div v-if="form.errors.minecraft_hover_text" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.minecraft_hover_text }}
                </div>
            </div>
            <div class="sm:col-span-2">
                <label for="minecraft_hover_text" class="block mb-2 text-sm font-medium text-foreground">
                    Minecraft Additional Homes
                </label>
                <input
                    v-model="form.additional_homes"
                    id="additional_homes"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    placeholder="0"
                    type="number"
                >
                <span class="block mt-2 text-xs font-medium text-subtle">
                    The text displayed when the Minecraft Display Name is hovered-over. Supports <a
                    class="text-primary" href="https://docs.advntr.dev/minimessage/format.html" target="_blank">MiniMessage</a> format
                </span>
                <div v-if="form.errors.additional_homes" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.additional_homes }}
                </div>
            </div>
            <div class="sm:col-span-2">
                <label for="display_priority" class="block mb-2 text-sm font-medium text-foreground">
                    Display Priority
                </label>
                <input
                    v-model="form.display_priority"
                    type="number"
                    id="display_priority"
                    class="block p-2.5 w-full text-sm text-foreground bg-surface-muted rounded-lg border border-border-strong focus:ring-focus focus:border-primary placeholder:text-subtle"
                    placeholder="1"
                >
                <span class="block mt-2 text-xs font-medium text-subtle">
                    If a player belongs to multiple roles of the same Role Type, the role with the <strong>highest</strong> Display Priority will be shown in chat.
                    <br/><br/>
                    Do not assign the same priority as another role in the same Role Type.
                </span>
                <div v-if="form.errors.display_priority" class="text-xs text-danger font-bold mt-2">
                    {{ form.errors.display_priority }}
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
