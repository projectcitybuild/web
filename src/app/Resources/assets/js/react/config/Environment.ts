export const Discord = {
    get API_URL() : string {
        return 'https://discordapp.com/api/guilds/161649330799902720/widget.json';
    }
}

export const PCB = {
    get API_URL() : string {
        return document.location.origin + '/api/';
    }
};