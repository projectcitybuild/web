import { AuthMiddleware, useAuth } from "@/hooks/useAuth";
import { NextPageWithLayout } from "@/pages/_app";
import { GetStaticProps } from "next"
import React, { ReactElement } from "react";
import DashboardLayout from "@/components/layouts/dashboard-layout";

interface Props {
}

const Page: NextPageWithLayout<Props> = (props): JSX.Element => {
  const { user } = useAuth({
    middleware: AuthMiddleware.AUTH,
  })

  return (
    <>
      <h1 className="text-heading-xl">Welcome back, {user?.username}</h1>
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

export const getStaticProps: GetStaticProps = async () => {
  return {
    props: {
      backgroundClassName: "background-secondary",
    }
  }
}

export default Page