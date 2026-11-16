<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import Card from '../../../shared/Components/Card.vue'
import MinecraftAvatar from '../../../manage/Components/MinecraftAvatar.vue'
import { distance, distanceFromNow, format } from '../../../manage/Utilities/DateFormatter'
import SuccessAlert from '../../../manage/Components/SuccessAlert.vue'
import FilledButton from '../../../manage/Components/FilledButton.vue'
import { computed } from 'vue'
import ErrorAlert from '../../../manage/Components/ErrorAlert.vue'
import SvgIcon from '../../../manage/Components/SvgIcon.vue'
import OutlinedButton from '../../../manage/Components/OutlinedButton.vue'
import { BuilderRankApplication, BuilderRankApplicationStatus } from '../../../manage/Data/BuilderRankApplication'
import Pill from '../../../manage/Components/Pill.vue'
import type { Role } from '../../../manage/Data/Role'
import AwaitingDecisionAlert from './Partials/AwaitingDecisionAlert.vue'
import BuilderRankDecision from './Partials/BuilderRankDecision.vue'
import BuilderRankDecisionForm from './Partials/BuilderRankDecisionForm.vue'
import { Icons } from '../../../manage/Icons'
import Badge from '../../../shared/Components/Badge.vue'
import PageHeader from '../../../shared/Components/PageHeader.vue'

interface Props {
    application: BuilderRankApplication,
    buildRoles: Role[],
    success?: string,
}
const props = defineProps<Props>()

const hasDecision = computed(() => props.application.status !== BuilderRankApplicationStatus.pending)
const status = computed(() => {
    switch (props.application.status) {
        case BuilderRankApplicationStatus.approved:
            return { variant: 'success' as const, label: 'Approved' }
        case BuilderRankApplicationStatus.denied:
            return { variant: 'danger' as const, label: 'Denied' }
        default:
            return { variant: 'warning' as const, label: 'Pending' }
    }
})

const waitingTime = computed(() => {
    if (props.application.closed_at) {
        return distance(props.application.created_at, props.application.closed_at)
    }
    return distanceFromNow(props.application.created_at)
})

const player = computed(() => props.application.account?.minecraft_account?.at(0))
const alts = computed(() => props.application.account?.minecraft_account?.slice(1) ?? [])
</script>

<template>
    <div>
        <Head :title="'Builder Rank Application: ' + application.id" />

        <SuccessAlert v-if="success" :message="success" class="mb-4"/>

        <PageHeader :title="`Builder Application #${application.id}`" class="mb-4">
            <template #actions>
                <Badge :variant="status.variant">{{ status.label }}</Badge>
                <span class="inline-flex items-center gap-2 text-sm text-muted">
                    <SvgIcon :svg="Icons.clock" class="size-5" />
                    Waiting Time: <strong class="text-foreground">{{ waitingTime }}</strong>
                </span>
            </template>
        </PageHeader>

        <div class="mt-4 flex flex-col lg:flex-row gap-4">
            <section class="lg:min-w-[260px]">
                <Card padding="none">
                    <template v-if="player">
                        <div class="p-4">
                            <h2 class="text-base font-semibold">Player</h2>
                        </div>

                        <div class="p-4 flex items-center gap-2">
                            <MinecraftAvatar :uuid="player.uuid" :size="36" class="shadow-lg" />
                            <a
                                :href="'/manage/players/' + player.id"
                                class="text-lg font-semibold text-primary-active underline-offset-2 hover:underline"
                            >
                                {{ player.alias }}
                            </a>
                        </div>
                    </template>

                    <template v-else>
                        <div class="p-4">
                            <h2 class="text-base font-semibold">Account</h2>
                        </div>

                        <div class="p-4 space-y-4">
                            <a
                                :href="'/manage/accounts/' + application.account_id"
                                class="font-medium text-primary-active underline-offset-2 hover:underline"
                            >
                                {{ application.account.username }}
                            </a>

                            <div class="flex items-center gap-2 text-red-500">
                                <span class="text-sm font-medium">This account has no linked players</span>
                            </div>
                        </div>
                    </template>

                    <div
                        v-if="alts.length > 0"
                        class="border-t border-border p-4"
                    >
                        <span class="text-xs text-muted">Also known as...</span>
                        <ul>
                            <li
                                v-for="player in alts"
                                class="py-2 flex gap-2 items-center"
                            >
                                <MinecraftAvatar :uuid="player.uuid" :size="24" />
                                <a
                                    :href="'/manage/players/' + player.id"
                                    class="text-sm font-medium text-primary-active underline-offset-2 hover:underline"
                                >
                                    {{ player.alias }}
                               </a>
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-4 border-t border-border p-4">
                        <h2 class="text-base font-semibold">Current Roles</h2>

                        <div class="flex flex-wrap gap-2">
                            <Pill
                                variant="default"
                                v-for="role in application.account.roles"
                            >
                                {{ role.name }}
                            </Pill>
                        </div>
                        <span v-if="application.account.roles.length === 0" class="text-sm text-muted">None</span>
                    </div>

                    <div class="p-4">
                        <a :href="'/rank-up/' + application.id">
                            <OutlinedButton variant="secondary" class="w-full">
                                <SvgIcon :svg="Icons.eye" />
                                View Application As Player
                            </OutlinedButton>
                        </a>
                    </div>
                </Card>
            </section>

            <section>
                <AwaitingDecisionAlert
                    v-if="!hasDecision"
                    class="mb-4"
                />

                <Card padding="none" class="mb-4">
                    <div class="border-b border-border p-4">
                        <h2 class="text-base font-semibold">Application</h2>
                    </div>

                    <ul class="p-4 space-y-8">
                        <li>
                            <h3 class="mb-2 text-sm font-semibold text-muted">Minecraft Alias</h3>
                            <div class="break-words whitespace-pre-wrap text-sm leading-6 text-foreground">{{ application.minecraft_alias }}</div>
                        </li>
                        <li>
                            <h3 class="mb-2 text-sm font-semibold text-muted">Current Builder Rank</h3>
                            <div class="break-words whitespace-pre-wrap text-sm leading-6 text-foreground">{{ application.current_builder_rank }}</div>
                        </li>
                        <li>
                            <h3 class="mb-2 text-sm font-semibold text-muted">Build Location</h3>
                            <div class="break-words whitespace-pre-wrap text-sm leading-6 text-foreground">{{ application.build_location }}</div>
                        </li>
                        <li>
                            <h3 class="mb-2 text-sm font-semibold text-muted">Build Description</h3>
                            <div class="break-words whitespace-pre-wrap text-sm leading-6 text-foreground">{{ application.build_description }}</div>
                        </li>
                        <li v-if="application.additional_notes">
                            <h3 class="mb-2 text-sm font-semibold text-muted">Additional Notes</h3>
                            <div class="break-words whitespace-pre-wrap text-sm leading-6 text-foreground">{{ application.additional_notes }}</div>
                        </li>
                    </ul>

                    <div class="border-t border-border p-4">
                        <div class="space-y-4">
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-muted">Submitted At</dt>
                                <dd class="text-sm text-foreground">
                                    {{ format(application.created_at) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </Card>

                <BuilderRankDecision
                    v-if="hasDecision"
                    :application="application"
                    class="mb-4"
                />
                <BuilderRankDecisionForm
                    v-else
                    :application="application"
                    :build-roles="buildRoles"
                />
            </section>
        </div>
    </div>
</template>
