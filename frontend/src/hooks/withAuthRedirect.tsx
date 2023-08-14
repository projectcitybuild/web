import { useAuth } from "@/providers/useAuth"
import { NextPage } from "next";
import { useRouter } from "next/router"
import { ReactElement } from "react"

function DefaultLoadingFallback(): ReactElement {
  // TODO
  return <></>;
}

export default function withAuthRedirect<
  Props extends JSX.IntrinsicAttributes = {},
  InitialProps = Props
>({
  WrappedComponent,
  LoadingComponent = DefaultLoadingFallback,
  expectedAuth,
  location,
}: {
  WrappedComponent: NextPage<Props, InitialProps>
  LoadingComponent?: NextPage
  expectedAuth: boolean
  location: string
}): NextPage<Props, InitialProps> {
  const WithAuthRedirectWrapper: NextPage<Props, InitialProps> = props => {
    const router = useRouter()
    const { isLoading, isAuthenticated } = useAuth()
    if (isLoading) {
      return <LoadingComponent/>
    }
    if (expectedAuth !== isAuthenticated) {
      router.push(location)
      return <></>
    }
    return <WrappedComponent {...props} />
  }
  return WithAuthRedirectWrapper
}