<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import Card from '../../../manage/Components/Card.vue'
import BackButton from '../../../manage/Components/BackButton.vue'
import MinecraftAvatar from '../../../manage/Components/MinecraftAvatar.vue'
import { distance, distanceFromNow, format } from '../../../manage/Utilities/DateFormatter'
import SuccessAlert from '../../../manage/Components/SuccessAlert.vue'
import ToolBar from '../../../manage/Components/ToolBar.vue'
import FilledButton from '../../../manage/Components/FilledButton.vue'
import type { BanAppeal } from '../../../manage/Data/BanAppeal'
import { computed } from 'vue'
import { BanAppealStatus } from '../../../manage/Data/BanAppealStatus'
import ErrorAlert from '../../../manage/Components/ErrorAlert.vue'
import SvgIcon from '../../../manage/Components/SvgIcon.vue'
import OutlinedButton from '../../../manage/Components/OutlinedButton.vue'
import { Icons } from '../../../manage/Icons'

interface Props {
    banAppeal: BanAppeal,
    success?: string,
}
const props = defineProps<Props>()

const form = useForm({
    status: null,
    decision_note: null,
})

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
                border: 'border-red-500',
                background: 'bg-red-500',
                label: 'Denied',
            }
        case BanAppealStatus.unbanned:
            return {
                border: 'border-green-500',
                background: 'bg-green-500',
                label: 'Unbanned',
            }
        case BanAppealStatus.tempBanned:
            return {
                border: 'border-green-500',
                background: 'bg-green-500',
                label: 'Downgraded to Temp Ban',
            }
        case BanAppealStatus.pending:
            return {}
    }
})

function submit() {
    form.put('/review/ban-appeals/' + props.banAppeal.id)
}
</script>

<template>
    <div>
        <Head :title="'Reviewing Ban Appeal: ' + banAppeal.id" />

        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <ToolBar>
            <template v-slot:left>
                <BackButton href="/review/ban-appeals"/>
            </template>

            <template v-slot:right>
                <SvgIcon :svg="Icons.clock" class="size-6" />
                Waiting Time: <strong>{{ waitingTime }}</strong>
            </template>
        </ToolBar>

        <div class="mt-4 flex flex-col lg:grid lg:grid-cols-2 gap-4">
            <section>
                <Card>
                    <div class="p-4">
                        <h2 class="font-bold">Appealer</h2>
                    </div>

                    <div class="space-y-4 border-t border-b border-gray-200 p-4">
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
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center gap-2">
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
                                        :href="'/manage/players/' + banAppeal.game_player_ban.banned_player.player_minecraft_id"
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
                                        :href="'/manage/players/' + banAppeal.game_player_ban.banner_player.player_minecraft_id"
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

                    <div class="space-y-4 border-t border-gray-200 p-4">
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
                    <div class="p-4 border-b border-gray-200">
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

                    <div class="p-4 border-t border-gray-200">
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
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="font-bold">Decision</h2>
                    </div>

                    <form @submit.prevent="submit" class="p-4">
                        <ErrorAlert v-if="form.hasErrors" :errors="form.errors" class="mb-4"/>

                        <div class="space-y-6">
                            <div>
                                <div class="flex items-center mb-4">
                                    <input
                                        v-model="form.status"
                                        :value="BanAppealStatus.denied"
                                        id="decision_denied"
                                        type="radio"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    >
                                    <label for="decision_denied" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                        Keep banned
                                    </label>
                                </div>
                                <div class="flex items-center mb-4">
                                    <input
                                        v-model="form.status"
                                        :value="BanAppealStatus.unbanned"
                                        id="decision_unban"
                                        type="radio"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    >
                                    <label for="decision_unban" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                                        Unban
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label for="decision_note" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Message to Player
                                </label>
                                <textarea
                                    v-model="form.decision_note"
                                    id="additional_info"
                                    rows="8"
                                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                ></textarea>
                                <span class="block mt-2 text-xs text-gray-400">
                                    This is the message emailed to the player regarding their application. <br />
                                    <strong>Please include the reasoning for your decision</strong>
                                </span>
                                <div v-if="form.errors.decision_note" class="text-xs text-red-500 font-bold mt-2">
                                    {{ form.errors.decision_note }}
                                </div>
                            </div>
                        </div>

                        <FilledButton
                            variant="danger"
                            :disabled="form.processing"
                            type="submit"
                            class="mt-8"
                        >
                            Finish and Close
                        </FilledButton>
                    </form>
                </Card>

                <Card v-else :class="'mt-4 ' + status.border">
                    <div :class="'p-4 rounded-t-md text-white ' + status.background">
                        <h2 class="font-bold">Decision: {{ status.label }}</h2>
                    </div>

                    <div class="p-4 italic">
                        {{ banAppeal.decision_note }}
                    </div>

                    <div class="p-4 border-t border-gray-200">
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
                                        :href="'/manage/players/' + banAppeal.decider_player.player_minecraft_id"
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
    </div>
</template>
