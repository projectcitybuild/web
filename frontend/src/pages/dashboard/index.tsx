import { NextPage } from "next"
import Link from "next/link";
import {useAuth} from "@/hooks/useAuth";
import {Routes} from "@/constants/routes";

interface Props {}

const Dashboard: NextPage<Props> = (props): JSX.Element => {
    const { user } = useAuth({
        middleware: 'auth',
    })

    console.log(user)

    return (
        <div>
            <h1>Dashboard</h1>

            <ul>
                <li>
                    <Link href={Routes.DASHBOARD}>Dashboard</Link>
                    <Link href={Routes.SECURITY}>Security</Link>
                    <Link href={Routes.LOGOUT}>Logout</Link>
                </li>
            </ul>
        </div>
    )
}

export default Dashboard