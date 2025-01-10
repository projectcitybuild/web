import { PlayerBan } from '../../manage/Data/PlayerBan'
import { Player } from '../../manage/Data/Player'
import { BanAppealStatus } from './BanAppealStatus'

export interface BanAppeal {
    id: number,
    explanation: string,
    email: string,
    status: BanAppealStatus,
    is_account_verified: boolean,
    game_player_ban: PlayerBan,
    created_at: string,
    updated_at: string,
    decided_at?: string,
    decider_player?: Player,
    decision_note?: string,
}
