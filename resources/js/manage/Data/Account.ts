import { Player } from './Player'
import { Badge } from './Badge'
import { Group } from './Group'
import { AccountActivation } from './AccountActivation'
import { Donation } from './Donation'

export interface Account {
    account_id: number,
    username: string,
    email: string,
    activated: boolean,
    is_totp_enabled: boolean,
    minecraft_account?: Player[],
    badges?: Badge[],
    groups?: Group[],
    activations?: AccountActivation[],
    donations?: Donation[],
    created_at: string,
    updated_at: string,
    last_login_at: string,
}
