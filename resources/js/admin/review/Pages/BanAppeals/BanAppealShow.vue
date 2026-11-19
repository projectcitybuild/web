<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import Card from '../../../shared/Components/Card.vue'
import ConfirmDialog from '../../../manage/Components/ConfirmDialog.vue'
import MinecraftAvatar from '../../../manage/Components/MinecraftAvatar.vue'
import { distance, distanceFromNow, format } from '../../../manage/Utilities/DateFormatter'
import SuccessAlert from '../../../manage/Components/SuccessAlert.vue'
import { computed, ref } from 'vue'
import { BanAppealStatus } from '../../../../generated/openapi-enums'
import ErrorAlert from '../../../manage/Components/ErrorAlert.vue'
import SvgIcon from '../../../manage/Components/SvgIcon.vue'
import OutlinedButton from '../../../manage/Components/OutlinedButton.vue'
import { Icons } from '../../../manage/Icons'
import Badge from '../../../shared/Components/Badge.vue'
import Button from '../../../shared/Components/Button.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import Textarea from '../../../shared/Components/Textarea.vue'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['review/BanAppeals/BanAppealShow']>()

const form = useForm({
    status: null,
    decision_note: null,
})
const confirmationDialog = ref<InstanceType<typeof ConfirmDialog> | null>(null)

const hasDecision = computed(() => props.banAppeal.decided_at != null)

const waitingTime = computed(() => {
    if (props.banAppeal.decided_at) {
        return distance(props.banAppeal.created_at, props.banAppeal.decided_at)
    }
    return distanceFromNow(props.banAppeal.created_at)
})

const status = computed(() => {
    switch (props.banAppeal.status) {
        case BanAppealStatus.denied:
            return {
                variant: 'danger' as const,
                label: 'Denied',
            }
        case BanAppealStatus.acceptedUnban:
            return {
                variant: 'success' as const,
                label: 'Unbanned',
            }
        case BanAppealStatus.acceptedTempban:
            return {
                variant: 'info' as const,
                label: 'Downgraded to Temp Ban',
            }
        case BanAppealStatus.pending:
            return {
                variant: 'warning' as const,
                label: 'Pending',
            }
        default:
            return {
                variant: 'warning' as const,
                label: 'Unknown',
            }
    }
})
const confirmationMessage = computed(() => {
    const decision = form.status === BanAppealStatus.acceptedUnban
        ? 'unban the player'
        : form.status === BanAppealStatus.denied
            ? 'keep the ban in place'
            : 'finish the review'

    return `You are about to ${decision} for Ban Appeal #${props.banAppeal.id}. This decision cannot be reversed.`
})

function submit() {
    form.put('/review/ban-appeals/' + props.banAppeal.id)
}

function requestConfirmation() {
    if (!form.processing) {
        confirmationDialog.value?.open()
    }
}

function confirmDecision() {
    if (form.processing) {
        return
    }

    confirmationDialog.value?.close()
    submit()
}
</script>

<template>
    <div>
        <Head :title="'Reviewing Ban Appeal: ' + banAppeal.id" />

        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <PageHeader
            :title="`Ban Appeal #${banAppeal.id}`"
            description="Review the player's request and the context for their ban."
            class="mb-4"
        >
            <template #actions>
                <Badge :variant="status.variant">{{ status.label }}</Badge>
                <span class="inline-flex items-center gap-2 text-sm text-muted">
                    <SvgIcon :svg="Icons.clock" class="size-5" />
                    {{ hasDecision ? 'Resolved in' : 'Open for' }}
                    <strong class="text-foreground">{{ waitingTime }}</strong>
                </span>
            </template>
        </PageHeader>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <section aria-label="Requester and ban details" class="space-y-4">
                <Card padding="none" class="overflow-hidden rounded-xl border border-border bg-surface">
                    <div class="p-4">
                        <h2 class="text-base font-semibold">Appealer</h2>
                    </div>

                    <div class="space-y-4 border-y border-border p-4">
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-muted">Email</dt>
                            <dd class="text-sm text-foreground">
                                {{ banAppeal.email }}
                            </dd>
                        </dl>
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-muted">Minecraft UUID</dt>
                            <dd class="text-sm text-foreground">
                                {{ banAppeal.minecraft_uuid }}
                            </dd>
                        </dl>
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-muted">Account</dt>
                            <dd class="text-sm text-foreground">
                                <a
                                    v-if="banAppeal.account"
                                    :href="'/manage/accounts/' + banAppeal.account.id"
                                    class="font-medium text-primary-active underline-offset-2 hover:underline"
                                >
                                    {{ banAppeal.account.username }}
                                </a>
                                <span v-else class="italic">Guest</span>
                            </dd>
                        </dl>
                    </div>
                </Card>

                <Card
                    v-if="banAppeal.game_player_ban"
                    padding="none"
                    class="overflow-hidden rounded-xl border border-border bg-surface"
                >
                    <div class="flex items-center justify-between gap-2 border-b border-border p-4">
                        <h2 class="text-base font-semibold">Player Ban</h2>

                        <a :href="'/manage/player-bans/' + banAppeal.game_player_ban.id + '/edit'">
                            <OutlinedButton variant="secondary">Edit</OutlinedButton>
                        </a>
                    </div>

                    <div class="p-4 space-y-4">
                        <dl>
                            <dt class="text-sm text-muted">Player</dt>
                            <dd class="text-sm text-foreground">
                                <div class="flex gap-2 items-center">
                                    <MinecraftAvatar :uuid="banAppeal.game_player_ban.banned_player.uuid" :size="24" />
                                    <a
                                        :href="'/manage/players/' + banAppeal.game_player_ban.banned_player.id"
                                        class="font-medium text-primary-active underline-offset-2 hover:underline"
                                    >
                                        {{ banAppeal.game_player_ban.banned_player.alias }}
                                    </a>
                                </div>
                            </dd>
                        </dl>
                        <dl>
                            <dt class="text-sm text-muted">Banned By</dt>
                            <dd class="text-sm text-foreground">
                                <div v-if="banAppeal.game_player_ban.banner_player" class="flex gap-2 items-center">
                                    <MinecraftAvatar :uuid="banAppeal.game_player_ban.banner_player.uuid" :size="24" />
                                    <a
                                        :href="'/manage/players/' + banAppeal.game_player_ban.banner_player.id"
                                        class="font-medium text-primary-active underline-offset-2 hover:underline"
                                    >
                                        {{ banAppeal.game_player_ban.banner_player.alias }}
                                    </a>
                                </div>
                                <span v-else>System</span>
                            </dd>
                        </dl>
                        <dl>
                            <dt class="text-sm text-muted">Reason</dt>
                            <dd class="text-sm italic text-foreground">
                                {{ banAppeal.game_player_ban.reason }}
                            </dd>
                        </dl>
                    </div>

                    <div class="space-y-4 border-t border-border p-4">
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-muted">Created At</dt>
                            <dd class="text-sm text-foreground">
                                {{ format(banAppeal.game_player_ban.created_at) }}
                            </dd>
                        </dl>
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-muted">Expires At</dt>
                            <dd class="text-sm text-foreground">
                                <span v-if="banAppeal.game_player_ban.expires_at">
                                    {{ format(banAppeal.game_player_ban.expires_at) }}
                                </span>
                                <span v-else>
                                    Never
                                </span>
                            </dd>
                        </dl>
                    </div>
                </Card>
            </section>

            <section aria-label="Appeal review" class="space-y-4">
                <Card
                    v-if="!hasDecision"
                    padding="none"
                    class="rounded-xl border border-warning bg-warning-soft p-4"
                >
                    <h2 class="mb-2 flex items-center gap-2 text-base font-semibold text-warning">
                        <SvgIcon :svg="Icons.hourglass" class="size-6" aria-hidden="true" />
                        Awaiting Decision
                    </h2>
                    <p class="text-sm leading-6 text-foreground">
                        Review the player's request and ban details, then record your decision in the review panel.
                    </p>
                </Card>

                <Card padding="none" class="overflow-hidden rounded-xl border border-border bg-surface">
                    <div class="border-b border-border p-4">
                        <h2 class="text-base font-semibold">Appeal request</h2>
                        <p class="mt-1 text-xs text-muted">Submitted {{ format(banAppeal.created_at) }}</p>
                    </div>

                    <div class="space-y-4 p-4">
                        <div>
                            <h3 class="text-sm font-semibold text-muted">Date of Ban</h3>
                            <div class="mt-2 rounded-none border border-border bg-surface-muted p-3 text-sm leading-6 text-foreground">
                                {{ banAppeal.date_of_ban }}
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-muted">Ban Reason</h3>
                            <div class="mt-2 rounded-none border border-border bg-surface-muted p-3 text-sm leading-6 text-foreground">
                                {{ banAppeal.ban_reason }}
                            </div>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-muted">Why should this ban be lifted?</h3>
                            <div class="mt-2 rounded-none border border-border bg-surface-muted p-4 text-sm leading-6 text-foreground">
                                {{ banAppeal.unban_reason }}
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-border p-4">
                        <div class="space-y-4">
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-muted">Created At</dt>
                                <dd class="text-sm text-foreground">
                                    {{ format(banAppeal.created_at) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </Card>

                <Card
                    v-if="!hasDecision"
                    padding="none"
                    class="overflow-hidden rounded-xl border border-border bg-surface"
                >
                    <div class="border-b border-border p-4">
                        <h2 class="text-base font-semibold">Decision</h2>
                        <p class="mt-1 text-sm text-muted">Record the outcome and the message the player will receive.</p>
                    </div>

                    <form @submit.prevent="requestConfirmation" class="p-4">
                        <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

                        <div class="space-y-6">
                            <div>
                                <div class="flex items-center mb-4">
                                    <input
                                        v-model="form.status"
                                        :value="BanAppealStatus.denied"
                                        id="decision_denied"
                                        type="radio"
                                        class="size-4 accent-primary focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                                    >
                                    <label for="decision_denied" class="ms-2 text-sm font-medium text-foreground">
                                        Keep banned
                                    </label>
                                </div>
                                <div class="flex items-center mb-4">
                                    <input
                                        v-model="form.status"
                                        :value="BanAppealStatus.acceptedUnban"
                                        id="decision_unban"
                                        type="radio"
                                        class="size-4 accent-primary focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-focus"
                                    >
                                    <label for="decision_unban" class="ms-2 text-sm font-medium text-foreground">
                                        Unban
                                    </label>
                                </div>
                            </div>
                            <div>
                                <Textarea
                                    v-model="form.decision_note"
                                    id="decision_note"
                                    rows="8"
                                    label="Message to Player"
                                    description="This message is emailed to the player. Include the reasoning for your decision."
                                    :error="form.errors.decision_note"
                                />
                            </div>
                        </div>

                        <Button
                            :variant="form.status === BanAppealStatus.acceptedUnban ? 'primary' : 'danger'"
                            :loading="form.processing"
                            type="button"
                            @click="requestConfirmation"
                            class="mt-8"
                        >
                            Finish and Close
                        </Button>
                    </form>
                </Card>

                <Card
                    v-else
                    padding="none"
                    class="overflow-hidden rounded-xl border border-border bg-surface"
                >
                    <div class="border-b border-border p-4">
                        <h2 class="flex items-center gap-2 text-base font-semibold">
                            Review outcome
                            <Badge :variant="status.variant">{{ status.label }}</Badge>
                        </h2>
                    </div>

                    <div class="p-4 text-sm leading-6 text-foreground">
                        {{ banAppeal.decision_note }}
                    </div>

                    <div class="border-t border-border p-4">
                        <div class="space-y-4">
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-muted">Decided At</dt>
                                <dd class="text-sm text-foreground">
                                    {{ format(banAppeal.decided_at) }}
                                </dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-muted">Decided By</dt>
                                <dd v-if="banAppeal.decider_player" class="flex items-center gap-2 text-sm text-foreground">
                                    <MinecraftAvatar :uuid="banAppeal.decider_player.uuid" :size="24" />
                                    <a
                                        :href="'/manage/players/' + banAppeal.decider_player.id"
                                        class="font-medium text-primary-active underline-offset-2 hover:underline"
                                    >
                                        {{ banAppeal.decider_player.alias }}
                                    </a>
                                </dd>
                                <dd v-else class="text-sm text-muted">Unavailable</dd>
                            </dl>
                        </div>
                    </div>
                </Card>
            </section>
        </div>

        <ConfirmDialog
            ref="confirmationDialog"
            title="Finish Ban Appeal?"
            :message="confirmationMessage"
            confirm-title="Finish and Close"
            :confirm-variant="form.status === BanAppealStatus.acceptedUnban ? 'primary' : 'danger'"
            :on-confirm="confirmDecision"
        />
    </div>
</template>
