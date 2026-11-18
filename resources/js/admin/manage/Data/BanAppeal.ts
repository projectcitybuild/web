import { BanAppealStatus } from './BanAppealStatus'
import { Player } from './Player'
import { PlayerBan } from './PlayerBan'

export interface BanAppeal {
    id: number,
    minecraft_uuid: string,
    date_of_ban: string,
    ban_reason?: string,
    unban_reason: string,
    email: string,
    status: BanAppealStatus,
    account?: App.Http.Data.AdminAccountData,
    game_player_ban: PlayerBan,
    created_at: string,
    updated_at: string,
    decided_at?: string,
    decider_player?: Player,
    decision_note?: string,
}
