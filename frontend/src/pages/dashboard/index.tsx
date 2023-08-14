import withAuth from "@/hooks/withAuth"
import { NextPageWithLayout } from "@/pages/_app";
import { useAuth } from "@/providers/useAuth"
import { GetStaticProps } from "next"
import React, { ReactElement } from "react";
import DashboardLayout from "@/components/layouts/dashboard-layout";

interface Props {}

const Page: NextPageWithLayout<Props> = (props): JSX.Element => {
  const { user } = useAuth()

  return (
    <>
      <h1 className="text-heading-xl">Welcome back, {user?.username}</h1>

      <div className="card">
        <div className="card-content">
          test
        </div>
      </div>
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

export default withAuth(Page)