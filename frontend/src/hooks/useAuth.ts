import api from "@/libs/http/api"
import querystring from "querystring"
import localApi from "@/libs/http/local_api";
import {query} from "express";

interface LoginCredentials {
    email: string
    password: string
}

interface AuthProvider {
    login: (credentials: LoginCredentials) => void
    logout: () => void
    isLoggedIn: boolean
}

export const useAuth = (): AuthProvider => {
    const apiClient = api('/api/proxy')
    // const localApiClient = localApi()

    const login = async (credentials: LoginCredentials) => {
        const params = querystring.stringify({
            email: credentials.email,
            password: credentials.password,
        })
        const response = await apiClient.post("login", params, {
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
        })
        console.log(response)

        // const accountParams = querystring.stringify(response.data.account)
        // await localApiClient.post("user/set", accountParams)
    }

    const logout = async () => {
        // const response = await apiClient.post("logout", {
        //     headers: { "Content-Type": "application/x-www-form-urlencoded" },
        // })
        // await localApiClient.get("user/destroy")
        // console.log(response)
    }

    const isLoggedIn = false

    return {
        login,
        logout,
        isLoggedIn,
    }
}
