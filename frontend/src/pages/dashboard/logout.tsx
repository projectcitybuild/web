import {withSessionSsr} from "@/libs/auth/session";
import {useAuth} from "@/hooks/useAuth";
import {NextPage} from "next";
import {useEffect} from "react";
import {useRouter} from "next/router";

const Logout: NextPage = (): JSX.Element => {
    const { logout } = useAuth()
    const router = useRouter()

    useEffect(() => {
        logout()
            .then(() => router.push("/"))
            .catch(console.error)
    }, [])

    return (
        <>
            Logging out...
        </>
    )
}

export const getServerSideProps = withSessionSsr(
    async function({ req, res }: any) { // TODO
        const user = req.session.user
        if (! user) {
            res.writeHead(307, { Location: '/' })
            res.end()
            return { props: {} }
        }
        return { props: {} }
    }
)

export default Logout