import * as querystring from "querystring";
import api from "@/libs/http/api";
import { AxiosInstance} from "axios";
import {TokenStorage} from "@/libs/auth/token_storage";

export class Auth {
    apiClient: AxiosInstance
    tokenStorage: TokenStorage

    constructor() {
        this.apiClient = api(process.env.NEXT_PUBLIC_API_BASE_URL as string)
        this.tokenStorage = new TokenStorage() // TODO: inject properly
    }

    async login(email: string, password: string) {
        const params = querystring.stringify({ email: email, password: password })
        const response = await this.apiClient.post("api/login", params, {
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
        })
        console.log(response)

        this.tokenStorage.set(response.data.access_token)
    }

    isLoggedIn(): boolean {
        return this.tokenStorage.get() != null
    }
}