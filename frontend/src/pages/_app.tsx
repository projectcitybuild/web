import "@/styles/globals.scss"
import { AuthProvider } from "@/providers/useAuth"
import type { AppProps } from "next/app"
import Head from "next/head";
import React, { ReactElement, ReactNode, useEffect } from "react";
import { config } from "@fortawesome/fontawesome-svg-core"
import "@fortawesome/fontawesome-svg-core/styles.css"
import { CookiesProvider } from "react-cookie";
import { NextPage } from "next";

config.autoAddCss = false

export type NextPageWithLayout<P = {}, IP = P> = NextPage<P, IP> & {
  getLayout?: (page: ReactElement) => ReactNode
}

type AppPropsWithLayout = AppProps & {
  Component: NextPageWithLayout
}

export default function App({ Component, pageProps }: AppPropsWithLayout) {
  const getLayout = Component.getLayout ?? ((page) => page)

  useEffect(() => {
    document.body.className = pageProps.backgroundClassName
  })

  return getLayout(
    <CookiesProvider>
      <AuthProvider>
        <Head>
          <meta name="viewport" content="width=device-width, initial-scale=1"/>
        </Head>
        <Component {...pageProps} />
      </AuthProvider>
    </CookiesProvider>
  )
}
