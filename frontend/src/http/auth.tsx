import axios from "axios"
import * as querystring from "querystring";

type ApiError = {
    message: string
    errors: {}
}

const api = axios.create({
    baseURL: "http://localhost",
    withCredentials: true,
    timeout: 1000,
    headers: { Accept: "application/json" },
})

export const login = async (email: string, password: string) => {
    try {
        await api.get("sanctum/csrf-cookie")

        const params = querystring.stringify({ email: email, password: password })
        const response = await api.post("api/login", params, {
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
        })
        console.log(response)
    } catch (error) {
        console.error(error)
    }
}