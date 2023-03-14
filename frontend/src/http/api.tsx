import axios, {AxiosError, AxiosInstance} from "axios";

const api = (baseURL: string) => axios.create({
    baseURL: baseURL,
    withCredentials: true,
    timeout: 10_000,
    headers: { Accept: "application/json" },
})

const withErrorHandling = (api: AxiosInstance) => {
    api.interceptors.response.use(
        (response) => response,
        (error) => {
            if (error instanceof AxiosError) {
                const body = error.response?.data
                if (body satisfies DisplayableError) {
                    throw new DisplayableError(body.message)
                }
            }
            return Promise.reject(error)
        }
    )
    return api;
}

export class DisplayableError extends Error {
    constructor(message: string) {
        super(message)
    }
}

const _api = (baseURL: string) => withErrorHandling(api(baseURL))

export default _api
