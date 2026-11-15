<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import Card from '../../../manage/Components/Card.vue'
import BackButton from '../../../manage/Components/BackButton.vue'
import ConfirmDialog from '../../../manage/Components/ConfirmDialog.vue'
import MinecraftAvatar from '../../../manage/Components/MinecraftAvatar.vue'
import { distance, distanceFromNow, format } from '../../../manage/Utilities/DateFormatter'
import SuccessAlert from '../../../manage/Components/SuccessAlert.vue'
import type { BanAppeal } from '../../../manage/Data/BanAppeal'
import { computed, ref } from 'vue'
import { BanAppealStatus } from '../../../manage/Data/BanAppealStatus'
import ErrorAlert from '../../../manage/Components/ErrorAlert.vue'
import SvgIcon from '../../../manage/Components/SvgIcon.vue'
import OutlinedButton from '../../../manage/Components/OutlinedButton.vue'
import { Icons } from '../../../manage/Icons'
import Badge from '../../../shared/Components/Badge.vue'
import Button from '../../../shared/Components/Button.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'
import Textarea from '../../../shared/Components/Textarea.vue'

interface Props {
    banAppeal: BanAppeal,
    success?: string,
}
const props = defineProps<Props>()

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
        case BanAppealStatus.unbanned:
            return {
                variant: 'success' as const,
                label: 'Unbanned',
            }
        case BanAppealStatus.tempBanned:
            return {
                variant: 'info' as const,
                label: 'Downgraded to Temp Ban',
            }
        case BanAppealStatus.pending:
            return {
                variant: 'warning' as const,
                label: 'Pending',
            }
    }
})
const confirmationMessage = computed(() => {
    const decision = form.status === BanAppealStatus.unbanned
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

        <PageHeader :title="`Ban Appeal #${banAppeal.id}`" class="mb-4">
            <template #actions>
                <Badge :variant="status.variant">{{ status.label }}</Badge>
                <BackButton href="/review/ban-appeals" />
                <span class="inline-flex items-center gap-2 text-sm text-muted">
                    <SvgIcon :svg="Icons.clock" class="size-5" />
                    Waiting Time: <strong class="text-foreground">{{ waitingTime }}</strong>
                </span>
            </template>
        </PageHeader>

        <div class="mt-4 flex flex-col lg:grid lg:grid-cols-2 gap-4">
            <section>
                <Card>
                    <div class="p-4">
                        <h2 class="font-bold">Appealer</h2>
                    </div>

                    <div class="space-y-4 border-y border-border p-4">
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                {{ banAppeal.email }}
                            </dd>
                        </dl>
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Minecraft UUID</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                {{ banAppeal.minecraft_uuid }}
                            </dd>
                        </dl>
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Account</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                <a
                                    v-if="banAppeal.account"
                                    :href="'/manage/accounts/' + banAppeal.account.account_id"
                                    class="text-blue-500 font-bold"
                                >
                                    <h1>{{ banAppeal.account.username }}</h1>
                                </a>
                                <span v-else class="italic">Guest</span>
                            </dd>
                        </dl>
                    </div>
                </Card>

                <Card class="mt-4" v-if="banAppeal.game_player_ban">
                    <div class="flex items-center justify-between gap-2 border-b border-border p-4">
                        <h2 class="font-bold">Player Ban</h2>

                        <a :href="'/manage/player-bans/' + banAppeal.game_player_ban.id + '/edit'">
                            <OutlinedButton variant="secondary">Edit</OutlinedButton>
                        </a>
                    </div>

                    <div class="p-4 space-y-4">
                        <dl>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Player</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                <div class="flex gap-2 items-center">
                                    <MinecraftAvatar :uuid="banAppeal.game_player_ban.banned_player.uuid" :size="24" />
                                    <a
                                        :href="'/manage/players/' + banAppeal.game_player_ban.banned_player.id"
                                        class="text-blue-500"
                                    >
                                        {{ banAppeal.game_player_ban.banned_player.alias }}
                                    </a>
                                </div>
                            </dd>
                        </dl>
                        <dl>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Banned By</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                <div v-if="banAppeal.game_player_ban.banner_player" class="flex gap-2 items-center">
                                    <MinecraftAvatar :uuid="banAppeal.game_player_ban.banner_player.uuid" :size="24" />
                                    <a
                                        :href="'/manage/players/' + banAppeal.game_player_ban.banner_player.id"
                                        class="text-blue-500"
                                    >
                                        {{ banAppeal.game_player_ban.banner_player.alias }}
                                    </a>
                                </div>
                                <span v-else>System</span>
                            </dd>
                        </dl>
                        <dl>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Reason</dt>
                            <dd class="text-sm text-gray-900 dark:text-white italic">
                                {{ banAppeal.game_player_ban.reason }}
                            </dd>
                        </dl>
                    </div>

                    <div class="space-y-4 border-t border-border p-4">
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Created At</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                {{ format(banAppeal.game_player_ban.created_at) }}
                            </dd>
                        </dl>
                        <dl class="flex items-center justify-between gap-2">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Expires At</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
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

            <section>
                <Card>
                    <div class="border-b border-border p-4">
                        <h2 class="font-bold">Ban Appeal</h2>
                    </div>

                    <div class="p-4">
                        <div>
                            <h3 class="text-gray-500 text-sm">Date of Ban</h3>
                            <span class="italic">{{ banAppeal.date_of_ban }}</span>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-gray-500 text-sm">Ban Reason</h3>
                            <span class="italic">{{ banAppeal.ban_reason }}</span>
                        </div>

                        <div class="mt-4">
                            <h3 class="text-gray-500 text-sm">Why should you be unbaned?</h3>
                            <span class="italic">{{ banAppeal.unban_reason }}</span>
                        </div>
                    </div>

                    <div class="border-t border-border p-4">
                        <div class="space-y-4">
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Created At</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    {{ format(banAppeal.created_at) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </Card>

                <Card class="mt-4" v-if="!hasDecision">
                    <div class="border-b border-border p-4">
                        <h2 class="font-bold">Decision</h2>
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
                                        class="size-4 accent-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                                    >
                                    <label for="decision_denied" class="ms-2 text-sm font-medium text-foreground">
                                        Keep banned
                                    </label>
                                </div>
                                <div class="flex items-center mb-4">
                                    <input
                                        v-model="form.status"
                                        :value="BanAppealStatus.unbanned"
                                        id="decision_unban"
                                        type="radio"
                                        class="size-4 accent-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
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
                            :variant="form.status === BanAppealStatus.unbanned ? 'primary' : 'danger'"
                            :loading="form.processing"
                            type="button"
                            @click="requestConfirmation"
                            class="mt-8"
                        >
                            Finish and Close
                        </Button>
                    </form>
                </Card>

                <Card v-else class="mt-4">
                    <div class="border-b border-border p-4">
                        <h2 class="flex items-center gap-2 font-bold">
                            Decision
                            <Badge :variant="status.variant">{{ status.label }}</Badge>
                        </h2>
                    </div>

                    <div class="p-4 italic">
                        {{ banAppeal.decision_note }}
                    </div>

                    <div class="border-t border-border p-4">
                        <div class="space-y-4">
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Decided At</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    {{ format(banAppeal.decided_at) }}
                                </dd>
                            </dl>
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Decided By</dt>
                                <dd class="text-sm text-gray-900 dark:text-white flex items-center gap-2">
                                    <MinecraftAvatar :uuid="banAppeal.decider_player.uuid" :size="24" />
                                    <a
                                        :href="'/manage/players/' + banAppeal.decider_player.id"
                                        class="text-blue-500"
                                    >
                                        {{ banAppeal.decider_player.alias }}
                                    </a>
                                </dd>
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
            :confirm-variant="form.status === BanAppealStatus.unbanned ? 'primary' : 'danger'"
            :on-confirm="confirmDecision"
        />
    </div>
</template>
