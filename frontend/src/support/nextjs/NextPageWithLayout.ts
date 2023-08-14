import { NextPage } from "next"
import { AppProps } from "next/app"
import { ReactElement, ReactNode } from "react"

export type NextPageWithLayout<
  Props = {},
  InitialProps = Props,
> = NextPage<Props, InitialProps> & {
  layout?: (page: ReactElement) => ReactNode
}

export type AppPropsWithLayout = AppProps & {
  Component: NextPageWithLayout
}