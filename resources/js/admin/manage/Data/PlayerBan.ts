import { Player } from './Player'

export interface PlayerBan {
    id: number,
    banned_player: Player,
    banner_player?: Player,
    reason?: string,
    additional_info?: string,
    expires_at: string | null,
    created_at: string | null,
    updated_at: string,
    unbanned_at?: string,
    unban_type?: UnbanType,
    unbanner_player?: Player,
}

export enum UnbanType {
    expired = 'expired',
    manual = 'manual',
    appealed = 'appealed',
}
