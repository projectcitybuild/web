import { Routes } from "@/constants/routes"
import withAuthRedirect from "@/hooks/withAuthRedirect"
import { NextPage } from "next"

export default function withAuth<Props extends JSX.IntrinsicAttributes>(
  WrappedComponent: NextPage<Props>,
  location = Routes.LOGIN,
): NextPage<Props> {
  return withAuthRedirect({
    WrappedComponent,
    location,
    expectedAuth: true,
  })
}