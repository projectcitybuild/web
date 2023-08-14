import withAuth from "@/hooks/withAuth"
import { useAuth } from "@/providers/useAuth"
import { NextPageWithLayout } from "@/support/nextjs/NextPageWithLayout"
import { GetStaticProps } from "next"
import React from "react";
import DashboardLayout from "@/components/layouts/dashboard-layout";

interface Props {}

const Page: NextPageWithLayout<Props> = (props): JSX.Element => {
  const { user } = useAuth()

  return (
    <DashboardLayout>
      <h1 className="text-heading-xl">Welcome back, {user?.username}</h1>

      <div className="card">
        <div className="card-content">
          test
        </div>
      </div>
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