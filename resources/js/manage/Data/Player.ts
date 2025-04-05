import { Account } from './Account'

export interface Player {
    player_minecraft_id: number,
    account?: Account,
    account_id?: number,
    uuid: string,
    alias?: string,
    nickname?: string,
    created_at: string,
    updated_at: string,
    last_synced_at?: string,
    last_seen_at?: string,
}
