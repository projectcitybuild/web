export interface Ban {
    game_ban_id: number,
    banned_alias_id: number,
    server_id: number,
    player_alias_at_ban: string,
    reason: string,
    created_at: number,
    expires_at: number,
    is_active: boolean,
    is_global_ban: boolean,
}

export interface Server {
    server_id: number,
    name: string,
    game_type: string,
}

export interface Alias {

}