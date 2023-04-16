import { NextPage } from "next"
import { useRouter } from "next/router"
import {withSessionSsr} from "@/libs/auth/session";
import Link from "next/link";
import {useAuth} from "@/hooks/useAuth";

interface Props {}

const Dashboard: NextPage<Props> = (props): JSX.Element => {
    const router = useRouter()

    const { user } = useAuth({
        middleware: 'auth',
        redirectIfAuthenticated: '/dashboard'
    })

    console.log(user)

    return (
        <div>
            <h1>Dashboard</h1>

            <ul>
                <li>
                    <Link href="/dashboard">Dashboard</Link>
                    <Link href="/dashboard/security">Security</Link>
                    <Link href="/dashboard/logout">Logout</Link>
                </li>
            </ul>
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