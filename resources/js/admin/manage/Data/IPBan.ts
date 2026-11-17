import { Player } from './Player'

export interface IPBan {
    id: number,
    banner_player?: Player,
    ip_address: string,
    reason?: string,
    created_at: string,
    updated_at: string,
    unbanned_at: string,
}
