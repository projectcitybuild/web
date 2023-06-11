import {AuthMiddleware, useAuth} from "@/hooks/useAuth";
import {NextPage} from "next";
import {useEffect} from "react";
import {useRouter} from "next/router";
import {Routes} from "@/constants/routes";

const Logout: NextPage = (): JSX.Element => {
    const { logout } = useAuth({
        middleware: AuthMiddleware.AUTH,
    })
    const router = useRouter()

    useEffect(() => {
        logout()
            .then(() => router.push(Routes.LOGIN))
            .catch(console.error)
    }, [])

    return (
        <>
            Logging out...
        </>
    )
}

export default Logout