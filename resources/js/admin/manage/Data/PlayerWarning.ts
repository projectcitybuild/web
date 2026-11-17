import { Player } from './Player'

export interface PlayerWarning {
    id: number,
    warned_player: Player,
    warner_player: Player,
    reason: string,
    additional_info?: string,
    weight: number,
    is_acknowledged: boolean,
    acknowledged_at?: string,
    created_at: string,
    updated_at: string,
}
