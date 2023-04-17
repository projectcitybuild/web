import axios, {AxiosError, AxiosInstance, AxiosResponse, InternalAxiosRequestConfig} from "axios";

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
                // TODO: clean up this mess...
                if (error.response?.status == 409 || error.response?.status == 423) {
                    return Promise.reject(error);
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

const authHttp = withInterceptors(
    axios.create({
        baseURL: process.env.NEXT_PUBLIC_API_BASE_URL,
        withCredentials: true,
        timeout: 10_000,
        headers: {
            Accept: "application/json",
        },
    })
)

export class DisplayableError extends Error {
    constructor(message: string) {
        super(message)
    }
}

export default authHttp
