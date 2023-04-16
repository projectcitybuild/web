import "@/styles/globals.scss"
import type { AppProps } from "next/app"
import Head from "next/head";
import React from "react";
import { config } from '@fortawesome/fontawesome-svg-core'
import '@fortawesome/fontawesome-svg-core/styles.css'
import {CookiesProvider} from "react-cookie";

config.autoAddCss = false

export default function App({ Component, pageProps }: AppProps) {
  return (
      <CookiesProvider>
        <Head>
          <meta name="viewport" content="width=device-width, initial-scale=1" />
        </Head>
        <Component {...pageProps} />
      </CookiesProvider>
  )
}
