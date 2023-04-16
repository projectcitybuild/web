import {useAuth} from "@/hooks/useAuth";
import {NextPage} from "next";
import {useEffect} from "react";
import {useRouter} from "next/router";

const Logout: NextPage = (): JSX.Element => {
    const { logout } = useAuth({
        middleware: 'auth',
    })
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

export default Logout