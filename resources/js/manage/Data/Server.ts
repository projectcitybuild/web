export interface Server {
    server_id: number,
    name: string,
    ip: string,
    port: number,
    web_port?: number,
    created_at: string,
    updated_at: string,
}
