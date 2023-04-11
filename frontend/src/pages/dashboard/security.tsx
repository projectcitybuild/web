import { NextPage } from "next"
import { useRouter } from "next/router"
import {withSessionSsr} from "@/libs/auth/session";
import Link from "next/link";
import {useEffect} from "react";
import api from "@/libs/http/api";

interface Props {}

const Dashboard: NextPage<Props> = (props): JSX.Element => {
    const router = useRouter()

    useEffect(() => {
        const apiClient = api('/api/proxy')
        const res = apiClient.get('user/confirm-password/status')
        console.log(res)
    })

    return (
        <div>
            <h1>Security Settings</h1>

            <ul>
                <li>
                    <Link href="/dashboard">Dashboard</Link>
                    <Link href="/dashboard/security">Security</Link>
                    <Link href="/dashboard/logout">Logout</Link>
                </li>
            </ul>

            <hr />

            <h2>2FA</h2>

        </div>
    )
}

export const getServerSideProps = withSessionSsr(
    async function({ req, res }: any) { // TODO
        const { user, accessToken } = req.session
        if (!user || !accessToken) {
            res.writeHead(307, { Location: '/login' })
            res.end()
            return { props: {} }
        }
        return {
            props: { user }
        }
    }
)

export default Dashboard