import axios from 'axios';
import * as Env from '../../config/Environment';

export const getAlbum = (callback) => {
    axios({
        method: 'get',
        url: 'https://api.imgur.com/3/album/' + Env.IMGUR_ALBUM_HASH + '/images',
        headers: {
            'Authorization': 'Client-ID ' + Env.IMGUR_CLIENT_ID,
        },
    })
    .then(response => {
        callback(response);
    })
    .catch(error => {
        console.error(error);
    });
}