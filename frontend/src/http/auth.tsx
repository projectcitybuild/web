import * as querystring from "querystring";
import api from "@/http/api";
import { AxiosInstance} from "axios";

export class Auth {
    apiClient: AxiosInstance

    constructor() {
        this.apiClient = api(process.env.NEXT_PUBLIC_API_BASE_URL as string)
    }

    async login(email: string, password: string) {
        await this.apiClient.get("sanctum/csrf-cookie")

        const params = querystring.stringify({ email: email, password: password })
        const response = await this.apiClient.post("api/login", params, {
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
        })
    }
}