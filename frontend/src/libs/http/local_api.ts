import axios, {AxiosError, AxiosInstance, AxiosResponse} from "axios";

const localApi = () => axios.create({
    baseURL: "/api",
    withCredentials: true,
    timeout: 10_000,
    headers: { Accept: "application/json" },
})

export default localApi
