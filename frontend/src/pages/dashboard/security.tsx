import { NextPage } from "next"
import { useRouter } from "next/router"
import Link from "next/link";
import {useEffect} from "react";
import http from "@/libs/http/http";
import {AuthMiddleware, useAuth} from "@/hooks/useAuth";

const Dashboard: NextPage = (props): JSX.Element => {
    const router = useRouter()

    const { user } = useAuth({
        middleware: AuthMiddleware.AUTH,
    })

    useEffect(() => {
        const res = http.get('user/confirm-password/status')
        console.log(res)
    })

    return (
        <div>
            <h1>Security Settings</h1>

            <ul>
                <li>
                    <Link href="/dashboard">Dashboard</Link>
                    <Link href="/dashboard/security">Security</Link>
                    <Link href="/logout">Logout</Link>
                </li>
            </ul>

            <hr />

            <h2>2FA</h2>

        </div>
    )
}

export default Dashboard