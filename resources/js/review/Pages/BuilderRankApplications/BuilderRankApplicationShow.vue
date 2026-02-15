<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import Card from '../../../manage/Components/Card.vue'
import BackButton from '../../../manage/Components/BackButton.vue'
import MinecraftAvatar from '../../../manage/Components/MinecraftAvatar.vue'
import { distance, distanceFromNow, format } from '../../../manage/Utilities/DateFormatter'
import SuccessAlert from '../../../manage/Components/SuccessAlert.vue'
import ToolBar from '../../../manage/Components/ToolBar.vue'
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

interface Props {
    application: BuilderRankApplication,
    buildRoles: Role[],
    success?: string,
}
const props = defineProps<Props>()

const hasDecision = computed(() => props.application.status !== BuilderRankApplicationStatus.pending)

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

        <ToolBar>
            <template v-slot:left>
                <BackButton href="/review/builder-ranks"/>
            </template>

            <template v-slot:right>
                <SvgIcon :svg="Icons.clock" class="size-6" />
                Waiting Time: <strong>{{ waitingTime }}</strong>
            </template>
        </ToolBar>

        <div class="mt-4 flex flex-col lg:flex-row gap-4">
            <section class="lg:min-w-[260px]">
                <Card>
                    <template v-if="player">
                        <div class="p-4">
                            <h2 class="font-bold">Player</h2>
                        </div>

                        <div class="p-4 flex items-center gap-2">
                            <MinecraftAvatar :uuid="player.uuid" :size="36" class="shadow-lg" />
                            <a
                                :href="'/manage/players/' + player.player_minecraft_id"
                                class="text-blue-500 text-xl font-bold"
                            >
                                <h1>{{ player.alias }}</h1>
                            </a>
                        </div>
                    </template>

                    <template v-else>
                        <div class="p-4">
                            <h2 class="font-bold">Account</h2>
                        </div>

                        <div class="p-4 space-y-4">
                            <a
                                :href="'/manage/accounts/' + application.account_id"
                                class="text-blue-500 font-bold"
                            >
                                <h1>{{ application.account.username }}</h1>
                            </a>

                            <div class="flex items-center gap-2 text-red-500">
                                <span class="text-sm font-bold">This account has no linked players</span>
                            </div>
                        </div>
                    </template>

                    <div
                        v-if="alts.length > 0"
                        class="p-4 border-t border-gray-200"
                    >
                        <span class="text-gray-500 text-xs">Also known as...</span>
                        <ul>
                            <li
                                v-for="player in alts"
                                class="py-2 flex gap-2 items-center"
                            >
                                <MinecraftAvatar :uuid="player.uuid" :size="24" />
                                <a
                                    :href="'/manage/players/' + player.player_minecraft_id"
                                    class="text-blue-500"
                                >
                                    {{ player.alias }}
                               </a>
                            </li>
                        </ul>
                    </div>

                    <div class="p-4 space-y-4 border-t border-gray-200">
                        <h2 class="font-bold">Current Roles</h2>

                        <div class="flex flex-wrap gap-2">
                            <Pill
                                variant="default"
                                v-for="role in application.account.roles"
                            >
                                {{ role.name }}
                            </Pill>
                        </div>
                        <span v-if="application.account.roles.length === 0" class="text-gray-500 text-sm">None</span>
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

                <Card class="mb-4">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="font-bold">Application</h2>
                    </div>

                    <ul class="p-4 space-y-8">
                        <li>
                            <h3 class="font-bold mb-2 text-sm">Minecraft Alias</h3>
                            {{ application.minecraft_alias }}
                        </li>
                        <li>
                            <h3 class="font-bold mb-2 text-sm">Current Builder Rank</h3>
                            {{ application.current_builder_rank }}
                        </li>
                        <li>
                            <h3 class="font-bold mb-2 text-sm">Build Location</h3>
                            {{ application.build_location }}
                        </li>
                        <li>
                            <h3 class="font-bold mb-2 text-sm">Build Description</h3>
                            {{ application.build_description }}
                        </li>
                        <li v-if="application.additional_notes">
                            <h3 class="font-bold mb-2 text-sm">Additional Notes</h3>
                            {{ application.additional_notes }}
                        </li>
                    </ul>

                    <div class="p-4 border-t border-gray-200">
                        <div class="space-y-4">
                            <dl class="flex items-center justify-between gap-2">
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Submitted At</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
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
