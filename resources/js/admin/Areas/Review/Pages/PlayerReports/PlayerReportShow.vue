<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { Head, router, useForm } from '@inertiajs/vue3'
import axios from 'axios'
import { Routes } from '../../../../Navigation/Routes'
import Badge from '../../../../Components/Badge.vue'
import Button from '../../../../Components/Button.vue'
import Card from '../../../../Components/Card.vue'
import ChoiceCard from '../../../../Components/Forms/ChoiceCard.vue'
import DangerZone from '../../../../Components/Forms/DangerZone.vue'
import ErrorAlert from '../../../../Components/ErrorAlert.vue'
import MinecraftAvatar from '../../../../Components/MinecraftAvatar.vue'
import PageHeader from '../../../../Components/PageHeader.vue'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import SvgIcon, { Icons } from '../../../../Components/SvgIcon.vue'
import Textarea from '../../../../Components/Textarea.vue'
import { format } from '../../../../Utilities/DateFormatter'

interface AssignableAccountSearchResult {
    id: number,
    name: string,
    alias: string,
    uuid: string,
}

const props = defineProps<AdminPageProps<'review/PlayerReports/PlayerReportShow'>>()

const assignmentForm = useForm({
    assignee_ids: props.report.assignees.map(assignee => assignee.id),
})
const accountSearch = ref('')
const searchResults = ref<AssignableAccountSearchResult[]>([])
const isSearching = ref(false)
const searchError = ref<string | null>(null)
const isSearchOpen = ref(false)
const activeSearchIndex = ref(-1)
const completionForm = useForm({
    completion_message: '',
})
const visibilityForm = useForm({
    visibility: props.report.visibility,
})
const deleteForm = useForm({})

const imagePreviewDialog = ref<HTMLDialogElement | null>(null)
const selectedImage = ref<AdminPageProps<'review/PlayerReports/PlayerReportShow'>['report']['attachments'][number] | null>(null)
const isCompleted = computed(() => props.report.status === 'completed')
const isAssignedToCurrentAccount = computed(() => assignmentForm.assignee_ids.includes(props.currentAccountId))

watch(accountSearch, (value, _, onCleanup) => {
    const search = value.trim()

    searchResults.value = []
    searchError.value = null
    isSearching.value = false
    activeSearchIndex.value = -1

    if (search.length < 2) {
        isSearchOpen.value = false

        return
    }

    isSearchOpen.value = true
    const controller = new AbortController()
    const timeout = setTimeout(async () => {
        isSearching.value = true

        try {
            const response = await axios.get<AssignableAccountSearchResult[]>(
                Routes.review.playerReports.searchAssignees(props.report.id),
                {
                    params: { search },
                    signal: controller.signal,
                },
            )

            searchResults.value = response.data.filter(account => !assignmentForm.assignee_ids.includes(account.id))
        } catch (error) {
            if (!controller.signal.aborted) {
                searchError.value = axios.isAxiosError<{ message?: string }>(error)
                    ? error.response?.data?.message ?? 'Unable to search eligible accounts.'
                    : error instanceof Error ? error.message : 'Unable to search eligible accounts.'
            }
        } finally {
            if (!controller.signal.aborted) {
                isSearching.value = false
            }
        }
    }, 250)

    onCleanup(() => {
        clearTimeout(timeout)
        controller.abort()
    })
})

function updateAssignees(assigneeIds: number[]): void {
    assignmentForm.assignee_ids = assigneeIds
    assignmentForm.put(Routes.review.playerReports.assignees(props.report.id), {
        onError: () => {
            assignmentForm.assignee_ids = props.report.assignees.map(assignee => assignee.id)
        },
    })
}

function addAssignee(account: AssignableAccountSearchResult): void {
    if (assignmentForm.processing || assignmentForm.assignee_ids.includes(account.id)) {
        return
    }

    accountSearch.value = ''
    isSearchOpen.value = false
    searchResults.value = []
    updateAssignees([...assignmentForm.assignee_ids, account.id])
}

function removeAssignee(accountId: number): void {
    if (assignmentForm.processing) {
        return
    }

    updateAssignees(assignmentForm.assignee_ids.filter(id => id !== accountId))
}

function closeAccountSearchIfFocusLeaves(event: FocusEvent): void {
    const container = event.currentTarget
    const nextTarget = event.relatedTarget

    if (container instanceof HTMLElement && (!(nextTarget instanceof Node) || !container.contains(nextTarget))) {
        isSearchOpen.value = false
    }
}

function handleAccountSearchKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        isSearchOpen.value = false

        return
    }

    if (event.key === 'ArrowDown' && searchResults.value.length > 0) {
        event.preventDefault()
        isSearchOpen.value = true
        activeSearchIndex.value = (activeSearchIndex.value + 1) % searchResults.value.length
    }

    if (event.key === 'ArrowUp' && searchResults.value.length > 0) {
        event.preventDefault()
        activeSearchIndex.value = activeSearchIndex.value <= 0
            ? searchResults.value.length - 1
            : activeSearchIndex.value - 1
    }

    if (event.key === 'Enter' && activeSearchIndex.value >= 0) {
        event.preventDefault()
        addAssignee(searchResults.value[activeSearchIndex.value])
    }
}

function assignToSelf(): void {
    router.post(Routes.review.playerReports.assignToSelf(props.report.id), {}, {
        onSuccess: () => {
            if (!assignmentForm.assignee_ids.includes(props.currentAccountId)) {
                assignmentForm.assignee_ids = [...assignmentForm.assignee_ids, props.currentAccountId]
            }
        },
    })
}

function completeReport(): void {
    completionForm.put(Routes.review.playerReports.complete(props.report.id))
}

function deleteReport(): void {
    if (!deleteForm.processing) {
        deleteForm.delete(Routes.review.playerReports.destroy(props.report.id))
    }
}

function updateVisibility(visibility: string | number): void {
    if (
        visibilityForm.processing
        || (visibility !== 'standard' && visibility !== 'administrators')
        || visibility === visibilityForm.visibility
    ) {
        return
    }

    visibilityForm.visibility = visibility
    visibilityForm.put(Routes.review.playerReports.visibility(props.report.id), {
        onError: () => {
            visibilityForm.visibility = props.report.visibility
        },
    })
}

function openImagePreview(attachment: AdminPageProps<'review/PlayerReports/PlayerReportShow'>['report']['attachments'][number]): void {
    selectedImage.value = attachment
    imagePreviewDialog.value?.showModal()
}

function closeImagePreview(event: MouseEvent): void {
    if (event.target === imagePreviewDialog.value) {
        imagePreviewDialog.value?.close()
    }
}
</script>

<template>
    <Head :title="`Player Report #${report.id}`" />

    <div class="space-y-4">
        <SuccessAlert v-if="success" :message="success" />
        <ErrorAlert v-if="visibilityForm.hasErrors" :errors="visibilityForm.errors" />

        <PageHeader
            :title="`Player Report #${report.id}`"
            :description="report.subject"
        >
            <template #actions>
                <Badge :variant="isCompleted ? 'success' : 'warning'">
                    {{ isCompleted ? 'Completed' : 'Open' }}
                </Badge>
                <Badge v-if="report.visibility === 'administrators'" variant="danger">
                    Administrators only
                </Badge>
            </template>
        </PageHeader>

        <div class="grid gap-4 lg:grid-cols-3">
            <section class="space-y-4 lg:col-span-2">
                <Card v-if="isCompleted" class="rounded-xl border border-success">
                    <h2 class="text-base font-semibold text-foreground">Case Closed</h2>
                    <p class="mt-1 text-sm text-muted">Completed {{ format(report.resolved_at) }}</p>
                    <p v-if="report.completion_message" class="mt-3 whitespace-pre-wrap leading-7 text-foreground">
                        {{ report.completion_message }}
                    </p>
                    <p v-else class="mt-3 text-sm text-muted">No message was sent to the reporter.</p>
                </Card>

                <Card class="rounded-xl border border-border bg-surface">
                    <h2 class="text-base font-semibold text-foreground">Report details</h2>
                    <p class="mt-1 text-sm text-muted">Submitted {{ format(report.created_at) }}</p>
                    <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm text-muted">Reporter</dt>
                            <dd class="text-foreground">{{ report.reporter_name }}</dd>
                            <dd v-if="report.reporter_account" class="text-xs text-muted">
                                Account: {{ report.reporter_account }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-muted">Reported player</dt>
                            <dd class="text-foreground">{{ report.reported_player_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-muted">Preferred contact</dt>
                            <dd class="whitespace-pre-wrap text-foreground">{{ report.reporter_contact }}</dd>
                        </div>
                        <div v-if="report.reporter_email">
                            <dt class="text-sm text-muted">Email</dt>
                            <dd class="break-all text-foreground">{{ report.reporter_email }}</dd>
                        </div>
                    </dl>

                    <div class="mt-5 border-t border-border pt-4">
                        <h3 class="text-sm font-semibold text-muted">What happened?</h3>
                        <p class="mt-2 whitespace-pre-wrap break-words leading-7 text-foreground">{{ report.details }}</p>
                    </div>
                </Card>

                <Card class="rounded-xl border border-border bg-surface">
                    <h2 class="text-base font-semibold text-foreground">Attachments</h2>
                    <ul v-if="report.attachments.length" class="mt-3 divide-y divide-border">
                        <li v-for="attachment in report.attachments" :key="attachment.id" class="py-3">
                            <button
                                v-if="attachment.preview_url"
                                type="button"
                                :aria-label="`Preview ${attachment.name}`"
                                class="group flex w-full flex-col items-start gap-2 text-start"
                                @click="openImagePreview(attachment)"
                            >
                                <img
                                    :src="attachment.preview_url"
                                    :alt="`Preview of ${attachment.name}`"
                                    loading="lazy"
                                    class="h-auto max-h-56 w-auto max-w-full rounded-lg border border-border object-contain transition-opacity group-hover:opacity-90"
                                />
                                <span class="text-sm font-medium text-primary-active underline-offset-2 group-hover:underline">
                                    {{ attachment.name }} <span class="text-muted">(click to enlarge)</span>
                                </span>
                            </button>
                            <a
                                v-else
                                :href="attachment.url"
                                class="text-sm font-medium text-primary-active underline-offset-2 hover:underline"
                            >
                                {{ attachment.name }}
                            </a>
                        </li>
                    </ul>
                    <p v-else class="mt-2 text-sm text-muted">No attachments were submitted.</p>
                </Card>
            </section>

            <aside class="space-y-4">
                <Card v-if="!isCompleted" class="rounded-xl border border-border bg-surface">
                    <h2 class="text-base font-semibold text-foreground">Assignment</h2>
                    <p class="mt-1 text-sm text-muted">
                        Assigned users
                    </p>

                    <ErrorAlert v-if="assignmentForm.hasErrors" :errors="assignmentForm.errors" class="mt-3" />

                    <div v-if="report.assignees.length" class="mt-3 flex flex-wrap gap-2">
                        <div
                            v-for="user in report.assignees"
                            :key="user.id"
                            class="flex min-w-0 items-center gap-2 rounded-full border border-border bg-surface-muted py-1 ps-1 pe-2"
                        >
                            <MinecraftAvatar
                                v-if="user.uuid"
                                :uuid="user.uuid"
                                :size="32"
                                class="shrink-0 overflow-hidden rounded-full"
                            />
                            <div
                                v-else
                                class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-semibold text-primary"
                                aria-hidden="true"
                            >
                                {{ (user.alias ?? user.name).charAt(0).toUpperCase() }}
                            </div>
                            <div class="min-w-0">
                                <p class="max-w-32 truncate text-sm font-medium text-foreground">
                                    {{ user.alias ?? user.name }}
                                </p>
                                <p v-if="user.alias && user.alias !== user.name" class="max-w-32 truncate text-xs text-muted">
                                    {{ user.name }}
                                </p>
                            </div>
                            <button
                                v-if="canUpdate"
                                type="button"
                                :aria-label="`Remove ${user.alias ?? user.name}`"
                                :disabled="assignmentForm.processing"
                                class="rounded-full p-1 text-muted hover:bg-surface hover:text-danger disabled:opacity-50"
                                @click="removeAssignee(user.id)"
                            >
                                <SvgIcon :svg="Icons.close" class="size-4" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                    <p v-else class="mt-3 text-sm text-muted">No users assigned yet.</p>

                    <Button
                        v-if="canUpdate && props.canAssignCurrentAccount && !isAssignedToCurrentAccount"
                        variant="secondary"
                        class="mt-3 w-full"
                        :disabled="assignmentForm.processing"
                        @click="assignToSelf"
                    >
                        Assign to me
                    </Button>

                    <div v-if="canUpdate" class="relative mt-4" @focusout="closeAccountSearchIfFocusLeaves">
                        <label for="player-report-assignee-search" class="mb-1 block text-sm font-medium text-foreground">
                            Add an eligible account
                        </label>
                        <input
                            id="player-report-assignee-search"
                            v-model="accountSearch"
                            type="search"
                            role="combobox"
                            aria-autocomplete="list"
                            aria-controls="player-report-assignee-results"
                            :aria-expanded="isSearchOpen"
                            :aria-busy="isSearching"
                            :aria-activedescendant="activeSearchIndex >= 0 ? `player-report-assignee-option-${activeSearchIndex}` : undefined"
                            autocomplete="off"
                            placeholder="Search by player alias"
                            class="block w-full rounded-lg border border-border-strong bg-surface px-3 py-2 text-sm text-foreground placeholder:text-subtle focus:border-primary focus:outline-hidden focus:ring-2 focus:ring-focus"
                            :disabled="assignmentForm.processing"
                            @focus="isSearchOpen = accountSearch.trim().length >= 2"
                            @keydown="handleAccountSearchKeydown"
                        />
                        <div
                            v-if="isSearchOpen && accountSearch.trim().length >= 2"
                            id="player-report-assignee-results"
                            class="absolute z-20 mt-1 max-h-64 w-full overflow-y-auto rounded-lg border border-border bg-surface shadow-lg"
                        >
                            <p v-if="isSearching" class="px-3 py-2 text-sm text-muted">
                                Searching accounts...
                            </p>
                            <p v-else-if="searchError" class="px-3 py-2 text-sm text-danger">
                                {{ searchError }}
                            </p>
                            <ul v-else-if="searchResults.length" role="listbox" class="py-1">
                                <li v-for="(account, index) in searchResults" :key="account.id" role="presentation">
                                    <button
                                        :id="`player-report-assignee-option-${index}`"
                                        type="button"
                                        role="option"
                                        :aria-selected="activeSearchIndex === index"
                                        class="flex w-full items-center gap-3 px-3 py-2 text-start hover:bg-surface-muted"
                                        :class="{ 'bg-surface-muted': activeSearchIndex === index }"
                                        @mouseenter="activeSearchIndex = index"
                                        @click="addAssignee(account)"
                                    >
                                        <MinecraftAvatar
                                            :uuid="account.uuid"
                                            :size="32"
                                            class="shrink-0 overflow-hidden rounded-full"
                                        />
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-medium text-foreground">
                                                {{ account.alias }}
                                            </span>
                                            <span class="block truncate text-xs text-muted">
                                                {{ account.name }}
                                            </span>
                                        </span>
                                    </button>
                                </li>
                            </ul>
                            <p v-else class="px-3 py-2 text-sm text-muted">
                                No eligible accounts found.
                            </p>
                        </div>
                    </div>
                    <p v-else class="mt-3 text-sm text-muted">
                        You do not have permission to change report assignments.
                    </p>
                </Card>

                <Card v-if="props.is_admin" class="rounded-xl border border-border bg-surface">
                    <h2 class="text-base font-semibold text-foreground">Visibility</h2>
                    <fieldset class="mt-3 grid gap-3">
                        <legend class="sr-only">Report visibility</legend>
                        <ChoiceCard
                            :model-value="visibilityForm.visibility"
                            name="player-report-visibility"
                            value="administrators"
                            title="Administrators-only"
                            description="Only administrators can view this report."
                            :disabled="visibilityForm.processing"
                            @update:model-value="updateVisibility"
                        />
                        <ChoiceCard
                            :model-value="visibilityForm.visibility"
                            name="player-report-visibility"
                            value="standard"
                            title="All staff"
                            description="All staff with report access can view this report."
                            :disabled="visibilityForm.processing"
                            @update:model-value="updateVisibility"
                        />
                    </fieldset>
                </Card>

                <Card v-if="canUpdate && !isCompleted" class="rounded-xl border border-border bg-surface">
                    <h2 class="text-base font-semibold text-foreground">Complete report</h2>
                    <p class="mt-1 text-sm text-muted">
                        Add an optional message for the reporter. If they provided an email address, the message will be emailed to them.
                    </p>
                    <form class="mt-4 space-y-3" @submit.prevent="completeReport">
                        <ErrorAlert v-if="completionForm.hasErrors" :errors="completionForm.errors" />
                        <Textarea
                            v-model="completionForm.completion_message"
                            label="Message to reporter"
                            rows="6"
                            :error="completionForm.errors.completion_message"
                        />
                        <Button type="submit" :loading="completionForm.processing" class="w-full">
                            Mark as complete
                        </Button>
                    </form>
                </Card>

                <DangerZone
                    v-if="props.is_admin"
                    title="Danger zone"
                    description="Permanently delete this report and all of its uploaded evidence."
                    confirm-message="This permanently deletes the report, its evidence, and all staff assignments. This action cannot be undone."
                    confirm-title="Permanently delete report"
                    button-label="Permanently delete report"
                    :disabled="deleteForm.processing"
                    @confirm="deleteReport"
                />
            </aside>
        </div>

        <dialog
            ref="imagePreviewDialog"
            aria-label="Image preview"
            class="fixed inset-0 m-auto max-h-[calc(100dvh-2rem)] w-[min(96vw,72rem)] max-w-none overflow-hidden rounded-xl border border-border bg-black/95 p-3 text-white shadow-2xl backdrop:bg-black/80"
            @click="closeImagePreview"
            @close="selectedImage = null"
        >
            <div class="mb-3 flex items-center justify-between gap-4">
                <p class="truncate text-sm font-medium">{{ selectedImage?.name }}</p>
                <button
                    type="button"
                    class="shrink-0 rounded-md px-3 py-2 text-sm font-semibold hover:bg-white/10 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-white"
                    @click="imagePreviewDialog?.close()"
                >
                    Close
                </button>
            </div>
            <img
                v-if="selectedImage?.preview_url"
                :src="selectedImage.preview_url"
                :alt="`Full-size preview of ${selectedImage.name}`"
                class="mx-auto h-auto max-h-[calc(100dvh-7rem)] w-auto max-w-full object-contain"
            />
        </dialog>
    </div>
</template>
