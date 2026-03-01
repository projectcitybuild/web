import { Player } from './Player'
import { Badge } from './Badge'
import { Role } from './Role'
import { AccountActivation } from './AccountActivation'
import { Donation } from './Donation'
import { EmailChangeRequest } from './EmailChangeRequest'

export interface Account {
    id: number,
    username: string,
    email: string,
    activated: boolean,
    is_totp_enabled: boolean,
    minecraft_account?: Player[],
    badges?: Badge[],
    roles?: Role[],
    activations?: AccountActivation[],
    donations?: Donation[],
    email_change_requests?: EmailChangeRequest[],
    created_at: string,
    updated_at: string,
    last_login_at: string,
}
