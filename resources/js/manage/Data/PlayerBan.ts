import { Player } from './Player'

export interface PlayerBan {
    id: number,
    banned_player: Player,
    banner_player?: Player,
    reason?: string,
    expires_at: string,
    created_at: string,
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
