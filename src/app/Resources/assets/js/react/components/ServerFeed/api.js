import axios from 'axios';
import{ PCB_API } from '../../config/Environment';

const apiInstance = axios.create({
    baseURL: PCB_API + 'servers/',
    timeout: 8000,
});

export const getServerList = () => {
    return apiInstance.get('all');
}