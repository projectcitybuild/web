import {NextApiRequest, NextApiResponse} from "next";
import {withSessionSsr} from "@/libs/auth/session";
import {User} from "@/libs/auth/user";

export function withLoggedInUser(loggedInHandler: (user: User) => void) {
    return withSessionSsr(
        async function({ req, res }: any) { // TODO
            const { user, accessToken } = req.session
            if (!user || !accessToken) {
                res.writeHead(307, { Location: '/login' })
                res.end()
                return { props: {} }
            }
            return loggedInHandler(user)
        }
    )
}