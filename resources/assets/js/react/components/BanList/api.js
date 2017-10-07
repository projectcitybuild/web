import axios from 'axios';
import{ PCB_API } from '../../config/Environment';

const apiInstance = axios.create({
    baseURL: PCB_API + 'bans/',
    timeout: 8000,
});

export const getBanList = (page = 1, sortBy, filters) => {
    return apiInstance.post('list', {
        take: 50,
        page: page,
        sort_field: sortBy.field,
        sort_direction: sortBy.direction,
    });
}