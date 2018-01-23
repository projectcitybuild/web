import axios, { AxiosRequestConfig, AxiosPromise } from 'axios';
import{ PCB } from '../../config/Environment';
import { Ban, Server, Alias } from './models';
import { Sort } from './BanList';

const apiInstance = axios.create({
    baseURL: PCB.API_URL + 'bans/',
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