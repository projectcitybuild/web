import * as axios from 'axios';
import{ PCB_API } from '../../config/Environment';
import { Ban, Server, Alias } from './models';
import { Sort } from './BanList';

const apiInstance = axios.create({
    baseURL: PCB_API + 'bans/',
    timeout: 8000,
});

export interface ApiResponse {
    data: {
        data: Array<Ban>,
        relations: {
            servers: Array<Server>,
            aliases: Array<Alias>,
        },
        meta: {
            count: number,
        }
    }
}

export const getBanList = (page: number = 1, sort: Sort) => {
    return apiInstance.post('list', {
        take: 50,
        page: page,
        sort_field: sort.field,
        sort_direction: sort.direction,
    });
}