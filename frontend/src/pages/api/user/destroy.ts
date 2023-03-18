import type { NextApiRequest, NextApiResponse } from 'next'
import {withApiSessionRoute} from "@/libs/auth/session";

type Response = {}

export default withApiSessionRoute(
    async function handler(req: NextApiRequest, res: NextApiResponse<Response>) {
        if (req.method !== 'GET') {
            res.status(405)
            return
        }
        await req.session.destroy()
        res.status(200).json({})
    }
)
