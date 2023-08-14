import { Routes } from "@/constants/routes"
import withAuthRedirect from "@/hooks/withAuthRedirect"
import { NextPage } from "next"

export default function withoutAuth<Props extends JSX.IntrinsicAttributes>(
  WrappedComponent: NextPage<Props>,
  location = Routes.DASHBOARD,
): NextPage<Props> {
  return withAuthRedirect({
    WrappedComponent,
    location,
    expectedAuth: false,
  })
}