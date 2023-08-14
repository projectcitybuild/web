import { ResponseBodyError, UnauthenticatedError, ValidationError } from "@/libs/http/HttpError"
import axios, { AxiosInstance, isAxiosError } from "axios";

type BackendError = {
  message: string
}

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
            if (isAxiosError(error) && error.response) {
                const status = error.response.status
                const body = error.response.data

                if (status === 401) {
                  return Promise.reject(new UnauthenticatedError())
                }
                if (status === 422) {
                  // TODO: extract the fields from the `errors` parameter
                  return Promise.reject(
                    new ValidationError({message: body.message})
                  )
                }

                // TODO: what do these codes represent again...?
                if (status == 409 || status == 423) {
                    return Promise.reject(error);
                }
                if (body satisfies BackendError) {
                    const casted = new ResponseBodyError({
                      message: body.message,
                    })
                    return Promise.reject(casted)
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

export default authHttp
