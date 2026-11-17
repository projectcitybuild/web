import axios from 'axios'

export interface PlayerLookup {
    uuid: string,
    alias: string,
}

export const lookupPlayer = async (search: string): Promise<PlayerLookup | null> => {
    try {
        const response = await axios.get(`https://playerdb.co/api/player/minecraft/${search}`)
        const data = response.data?.data?.player
        if (!data) return null
        return {
            uuid: data.id,
            alias: data.username,
        }
    } catch (error) {
        const message = error.response?.data.message
        if (message) {
            throw new Error(message)
        }
        throw error
    }
}

export const lookupAlias = async (uuid: string): Promise<string | null> => {
    const lookup = await lookupPlayer(uuid)
    return lookup?.alias
}
