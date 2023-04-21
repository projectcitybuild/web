export interface Server {
    server_id: number
    name: string
    ip: string
    ip_alias?: string
    port?: string
    is_online: boolean
    is_visible: boolean
    num_of_players: number
    num_of_slots: number
}