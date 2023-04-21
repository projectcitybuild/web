import { NextPage } from "next"
import { useRouter } from "next/router"
import Link from "next/link";
import {useEffect, useState} from "react";
import http from "@/libs/http/http";
import {AuthMiddleware, useAuth} from "@/hooks/useAuth";

const Dashboard: NextPage = (props): JSX.Element => {
    const router = useRouter()

    const { user, twoFactorEnable } = useAuth({
        middleware: AuthMiddleware.AUTH,
    })

    const [ loading, setLoading ] = useState(false)

    const onEnableTwoFactor = async () => {
        setLoading(true)
        try {
            await twoFactorEnable()
        } catch (error) {

        } finally {
            setLoading(false)
        }
    }

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

            <section className="section">
                <h2>2FA</h2>

                <button
                    className={`button is-primary ${loading ? 'is-loading' : ''}`}
                    disabled={loading}
                    onClick={onEnableTwoFactor}
                >Enable 2FA</button>
            </section>
        </div>
    )
}

export default Dashboard