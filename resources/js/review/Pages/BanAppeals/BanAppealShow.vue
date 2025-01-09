<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import Card from '../../../manage/Components/Card.vue'
import BackButton from '../../../manage/Components/BackButton.vue'
import MinecraftAvatar from '../../../manage/Components/MinecraftAvatar.vue'
import { format } from '../../../manage/Utilities/DateFormatter'
import SuccessAlert from '../../../manage/Components/SuccessAlert.vue'
import Spinner from '../../../manage/Components/Spinner.vue'
import ToolBar from '../../../manage/Components/ToolBar.vue'
import OutlinedButton from '../../../manage/Components/OutlinedButton.vue'
import SvgIcon from '../../../manage/Components/SvgIcon.vue'
import FilledButton from '../../../manage/Components/FilledButton.vue'
import { BanAppeal } from '../../Data/BanAppeal'

interface Props {
    banAppeal: BanAppeal,
    success?: string,
}
const props = defineProps<Props>()
console.log(props.banAppeal)
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
                <FilledButton variant="primary">
                    <SvgIcon icon="alert" />
                    Make Decision
                </FilledButton>
            </template>
        </ToolBar>

        <div class="mt-4 grid grid-cols-2 gap-4 md:flex-row">
            <section>
                <Card>
                    <div class="p-4">
                        <h2 class="font-bold">Player</h2>

                        <!--                        <MinecraftAvatar :uuid="player.uuid" :size="96" class="shadow-lg" />-->
<!--                        <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">{{ player.alias }}</h1>-->
<!--                        <span class="mt-2 text-sm text-gray-500">{{ player.uuid }}</span>-->
                    </div>
                </Card>

                <Card class="mt-4">
                    <div class="p-4">
                        <h2 class="font-bold">Player Ban</h2>

                        <div class="mt-4 space-y-4">
                            <dl>
                                <dt class="text-sm text-gray-500 dark:text-gray-400">Banned By</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    <div v-if="banAppeal.game_player_ban.banner_player" class="flex gap-2 items-center">
                                        <MinecraftAvatar uuid="banAppeal.game_player_ban.banner_player.uuid" />
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
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    {{ banAppeal.game_player_ban.reason }}
                                </dd>
                            </dl>
                        </div>
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
                    <div class="p-4">
                        <h2 class="font-bold">Ban Appeal</h2>

                        <div class="mt-4">
                            {{ banAppeal.explanation }}
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
            </section>
        </div>
    </div>
</template>
