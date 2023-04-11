import axios, {AxiosError, AxiosInstance, AxiosResponse, InternalAxiosRequestConfig} from "axios";

const api = (baseURL: string) => axios.create({
    baseURL: baseURL,
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
                // TODO: clean this up
                if (error.status == 419 && error.config!.url != "sanctum/csrf-cookie") {
                    console.log("Requesting XSRF token...")

                    await api.get("sanctum/csrf-cookie")
                    return await api(error.config!) // Retry request
                }

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

const _api = (baseURL: string) => withInterceptors(api(baseURL))

export default _api
