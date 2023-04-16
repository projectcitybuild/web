import { NextPage } from "next"
import Link from "next/link";
import {useAuth} from "@/hooks/useAuth";

interface Props {}

const Dashboard: NextPage<Props> = (props): JSX.Element => {
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

export default Dashboard