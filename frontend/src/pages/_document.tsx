import Document, { Html, Head, Main, NextScript, DocumentContext, DocumentInitialProps } from "next/document"
import { ReactElement } from "react"

export default class CustomDocument extends Document {
  render(): ReactElement {
    const pageProps = this.props?.__NEXT_DATA__?.props?.pageProps

    return (
      <Html lang="en">
        <Head />
        <body className={ pageProps.backgroundClassName }>
        <Main />
        <NextScript />
        </body>
      </Html>
    )
  }
}
