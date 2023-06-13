import { AuthMiddleware, useAuth } from "@/hooks/useAuth";
import { NextPageWithLayout } from "@/pages/_app";
import React, { ReactElement } from "react";
import DashboardLayout from "@/components/layouts/dashboard-layout";

interface Props {
}

const Page: NextPageWithLayout<Props> = (props): JSX.Element => {
  const {user} = useAuth({
    middleware: AuthMiddleware.AUTH,
  })

  console.log(user)

  return (
    <>
      TODO
    </>
  )
}

Page.getLayout = function getLayout(page: ReactElement) {
  return (
    <DashboardLayout>
      {page}
    </DashboardLayout>
  )
}

export default Page