import type { NextApiRequest, NextApiResponse } from 'next'
import {withApiSessionRoute} from "@/libs/auth/session";

type Response = {}

export default withApiSessionRoute(
    async function handler(req: NextApiRequest, res: NextApiResponse<Response>) {
        if (req.method !== 'POST') {
            res.status(405)
            return
        }
        req.session.user = req.body
        await req.session.save()
        res.status(200).json({})
    }
)
