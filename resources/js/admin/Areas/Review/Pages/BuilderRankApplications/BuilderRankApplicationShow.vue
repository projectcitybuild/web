<script setup lang="ts">
import { Routes } from '../../../../Navigation/Routes'
import { Head } from '@inertiajs/vue3'
import Card from '../../../../Components/Card.vue'
import MinecraftAvatar from '../../../../Components/MinecraftAvatar.vue'
import { distance, distanceFromNow, format } from '../../../../Utilities/DateFormatter'
import SuccessAlert from '../../../../Components/SuccessAlert.vue'
import { computed } from 'vue'
import { ApplicationStatus } from '../../../../../generated/openapi-enums'
import SvgIcon from '../../../../Components/SvgIcon.vue'
import OutlinedButton from '../../../../Components/OutlinedButton.vue'
import Pill from '../../../../Components/Pill.vue'
import AwaitingDecisionAlert from './Partials/AwaitingDecisionAlert.vue'
import BuilderRankDecision from './Partials/BuilderRankDecision.vue'
import BuilderRankDecisionForm from './Partials/BuilderRankDecisionForm.vue'
import { Icons } from '../../../../Components/SvgIcon.vue'
import Badge from '../../../../Components/Badge.vue'
import PageHeader from '../../../../Components/PageHeader.vue'

const props = defineProps<AdminSharedPageProps & AdminInertiaPageProps['review/BuilderRankApplications/BuilderRankApplicationShow']>()

const hasDecision = computed(() => props.application.status !== ApplicationStatus.pending)
const status = computed(() => {
    switch (props.application.status) {
        case ApplicationStatus.approved:
            return { variant: 'success' as const, label: 'Approved' }
        case ApplicationStatus.denied:
            return { variant: 'danger' as const, label: 'Denied' }
        case ApplicationStatus.pending:
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

        <PageHeader
            :title="`Builder Application #${application.id}`"
            description="Review the applicant's builder-rank request and submitted build."
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

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-[minmax(16rem,0.8fr)_minmax(0,1.8fr)]">
            <section aria-label="Applicant details">
                <Card padding="none" class="overflow-hidden rounded-xl border border-border bg-surface">
                    <template v-if="player">
                        <div class="p-4">
                            <h2 class="text-base font-semibold">Player</h2>
                        </div>

                        <div class="p-4 flex items-center gap-2">
                            <MinecraftAvatar :uuid="player.uuid" :size="36" class="shadow-lg" />
                            <a
                                :href="Routes.manage.players.show(player.id)"
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
                                :href="Routes.manage.accounts.show(application.account_id)"
                                class="font-medium text-primary-active underline-offset-2 hover:underline"
                            >
                                {{ application.account?.username ?? 'Account' }}
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
                                    :href="Routes.manage.players.show(player.id)"
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
                                v-for="role in application.account?.roles ?? []"
                            >
                                {{ role.name }}
                            </Pill>
                        </div>
                        <span v-if="(application.account?.roles?.length ?? 0) === 0" class="text-sm text-muted">None</span>
                    </div>

                    <div class="p-4">
                        <a :href="Routes.rankUp(application.id)">
                            <OutlinedButton variant="secondary" class="w-full">
                                <SvgIcon :svg="Icons.eye" />
                                View Application As Player
                            </OutlinedButton>
                        </a>
                    </div>
                </Card>
            </section>

            <section aria-label="Application review" class="min-w-0">
                <AwaitingDecisionAlert
                    v-if="!hasDecision"
                    class="mb-4"
                />

                <Card
                    padding="none"
                    class="mb-4 overflow-hidden rounded-xl border border-border bg-surface"
                >
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
