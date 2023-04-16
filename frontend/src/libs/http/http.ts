import axios, {AxiosError, AxiosInstance, AxiosResponse, InternalAxiosRequestConfig} from "axios";

const http = axios.create({
    baseURL: process.env.NEXT_PUBLIC_API_BASE_URL,
    withCredentials: true,
    timeout: 10_000,
    headers: {
        Accept: "application/json",
    },
})

const withInterceptors = (api: AxiosInstance) => {
    api.interceptors.request.use(
        (req) => {
            return Promise.resolve(req)
        },
        (error) => Promise.reject(error),
    )
    api.interceptors.response.use(
        (response) => response,
        async (error) => {
            if (error instanceof AxiosError) {
                const body = error.response?.data
                if (body satisfies DisplayableError) {
                    throw new DisplayableError(body.message)
                }
            }
            return Promise.reject(error)
        }
    )
    return api
}

export class DisplayableError extends Error {
    constructor(message: string) {
        super(message)
    }
}

const _http = withInterceptors(http)

export default _http
