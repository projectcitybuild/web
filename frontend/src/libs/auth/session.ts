import {getIronSession, IronSessionOptions} from "iron-session"
import {withIronSessionApiRoute, withIronSessionSsr} from "iron-session/next"
import { User } from "@/libs/auth/user"
import {GetServerSidePropsContext, GetServerSidePropsResult, NextApiHandler} from "next"

export const sessionOptions: IronSessionOptions = {
    cookieName: 'pcb-web',
    cookieOptions: {
        // secure: true should be used in production (HTTPS) but can't be used in development (HTTP)
        secure: process.env.NODE_ENV === 'production',

        // Uncomment later to kill the cookie when the browser is closed
        // maxAge: undefined
    },
    password: process.env.IRON_SESSION_COOKIE_PASSWORD as string,
}

// Module augmentation so that `req.session` autocompletes to
// our expected data type
declare module "iron-session" {
    interface IronSessionData {
        user?: User,
        accessToken: string,
    }
}

// Puts the cookie data as the `req.session` property, for use
// in `getServerSideProps()`
export function withSessionSsr<
    P extends { [key: string]: unknown } = { [key: string]: unknown },
>(
    handler: (
        context: GetServerSidePropsContext,
    ) => GetServerSidePropsResult<P> | Promise<GetServerSidePropsResult<P>>,
) {
    return withIronSessionSsr(handler, sessionOptions)
}

// Puts the cookie data as the `req.session` property, for use
// with NodeJS API routes
export function withApiSessionRoute(handler: NextApiHandler) {
    return withIronSessionApiRoute(handler, sessionOptions)
}

// TODO: add types
export function getSession(req: any, res: any) {
    return getIronSession(req, res, sessionOptions)
}