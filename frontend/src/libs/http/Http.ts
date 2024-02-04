import {
  ResourceConflictError,
  ResourceLockedError,
  ResponseBodyError,
  UnauthenticatedError,
  ValidationError
} from "@/libs/http/HttpError"
import axios, { AxiosInstance, isAxiosError } from "axios";

type BackendError = {
  message: string
}

interface PayloadBody {
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

                const mappedError = errorFromStatus({status: status, body: body})
                if (mappedError) {
                  return Promise.reject(mappedError)
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

const errorFromStatus = (props: {status: number, body: PayloadBody}): Error|null => {
  if (props.status === 401) {
    return new UnauthenticatedError()
  }
  if (props.status === 409) {
    return new ResourceConflictError({
      message: props.body.message,
    })
  }
  if (props.status === 422) {
    // TODO: extract the fields from the `errors` parameter
    return new ValidationError({
      message: props.body.message,
    })
  }
  if (props.status === 423) {
    return new ResourceLockedError()
  }
  return null
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
