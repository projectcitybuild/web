import { NextPage } from "next"
import { useRouter } from "next/router"
import {withSessionSsr} from "@/libs/auth/session";
import Link from "next/link";

interface Props {}

const Dashboard: NextPage<Props> = (props): JSX.Element => {
    const router = useRouter()

    return (
        <div>
            Dashboard

            <ul>
                <li>
                    <Link href="dashboard/logout">Logout</Link>
                </li>
            </ul>
        </div>
    )
}

// export const getServerSideProps = withSessionSsr(
//     async function({ req, res }: any) { // TODO
//         const user = req.session.user
//         if (! user) {
//             res.writeHead(307, { Location: '/login' })
//             res.end()
//             return { props: {} }
//         }
//         return {
//             props: { user }
//         }
//     }
// )

export default Dashboard