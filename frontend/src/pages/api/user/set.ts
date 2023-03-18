import type { NextApiRequest, NextApiResponse } from 'next'
import {withApiSessionRoute} from "@/libs/auth/session";

type Response = {}

export default withApiSessionRoute(
    async function route(req: NextApiRequest, res: NextApiResponse<Response>) {
      req.session.user = req.body.user
      await req.session.save()
      res.send({ ok: true })
    }
)
