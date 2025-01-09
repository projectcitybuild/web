import { PlayerBan } from '../../manage/Data/PlayerBan'

export interface BanAppeal {
    id: number,
    explanation: string,
    email: string,
    status: BanAppealStatus,
    game_player_ban: PlayerBan,
    created_at: string,
    updated_at: string,
}
