import httpProxy from 'http-proxy'
import url from 'url'
import {NextApiRequest, NextApiResponse} from "next";
import {withApiSessionRoute} from "@/libs/auth/session";

const API_URL = process.env.API_BASE_URL

const proxy = httpProxy.createProxyServer()

// Disable `bodyParser`, otherwise Next.js will read and parse
// the request body before we can forward the request to our
// actual API. Allowing this would be undesirable since we don't
// need to anything but stream it to the API
export const config = {
    api: {
        bodyParser: false,
    },
}

export default withApiSessionRoute(
    (req: NextApiRequest, res: NextApiResponse<any>) => {
        return new Promise((resolve, reject) => {
            if (!req.url) {
                res.status(400);
                return;
            }
            const originalPath = url.parse(req.url).pathname

            const accessToken = req.session.accessToken
            if (accessToken) {
                req.headers['access-token'] = accessToken
            }

            req.url = req.url.replace(/^\/api\/proxy/, '')

            // Don't forward cookies to the API
            req.headers.cookie = ''

            // Intercept login API response so that we can strip out the
            // access token and store it
            let didInterceptResponse = false
            if (originalPath === '/api/proxy/login') {
                didInterceptResponse = true

                proxy.once('proxyRes', (proxyRes, interceptedReq, interceptedRes) => {
                    let responseBody = ''
                    proxyRes.on('data', (chunk) => responseBody += chunk)

                    proxyRes.on('end', async () => {
                        const json = JSON.parse(responseBody)
                        const { account } = json
                        const accessToken = json.access_token

                        if (accessToken) {
                            req.session.accessToken = accessToken
                            req.session.user = account
                            await req.session.save()

                            // Don't expose the access token to the client
                            json["access_token"] = null
                        }

                        const statusCode = proxyRes.statusCode ?? interceptedRes.statusCode
                        res.status(statusCode).json(json)
                        resolve(responseBody)
                    })
                })
            }

            proxy.once('error', reject)

            proxy.web(req, res, {
                target: API_URL,

                // Don't autoRewrite because we manually rewrite
                // the URL in the route handler.
                autoRewrite: false,

                // In case we're dealing with a login request,
                // we need to tell http-proxy that we'll handle
                // the client-response ourselves (since we don't
                // want to pass along the auth token).
                selfHandleResponse: didInterceptResponse,
            })
        })
    }
)