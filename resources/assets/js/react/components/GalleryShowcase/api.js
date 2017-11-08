import axios from 'axios';
import{ PCB_API } from '../../config/Environment';

const apiInstance = axios.create({
    baseURL: PCB_API + 'gallery/',
    timeout: 8000,
});

export const getAlbum = (callback) => {
    return apiInstance.get('featured')
        .then(response => {
            callback(response);
        })
        .catch(error => {
            console.error(error);
        });
}