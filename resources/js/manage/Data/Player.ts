import { Account } from './Account'

export interface Player {
    player_minecraft_id: number,
    account?: Account,
    account_id?: number,
    uuid: string,
    alias?: string,
    created_at: string,
    updated_at: string,
}
