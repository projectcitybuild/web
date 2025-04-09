import { BanAppealStatus } from './BanAppealStatus'
import { Player } from './Player'
import { PlayerBan } from './PlayerBan'
import { Account } from './Account'

export interface BanAppeal {
    id: number,
    minecraft_uuid: string,
    ban_reason?: string,
    unban_reason: string,
    email: string,
    status: BanAppealStatus,
    account?: Account,
    game_player_ban: PlayerBan,
    created_at: string,
    updated_at: string,
    decided_at?: string,
    decider_player?: Player,
    decision_note?: string,
}
