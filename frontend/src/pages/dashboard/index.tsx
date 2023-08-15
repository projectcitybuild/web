import withAuth from "@/hooks/withAuth"
import { useAuth } from "@/providers/useAuth"
import { GetStaticProps } from "next"
import React from "react";
import DashboardLayout from "@/components/layouts/dashboard-layout";

const Page = (): JSX.Element => {
  const { user } = useAuth()

  return (
    <DashboardLayout>
      <h1 className="text-heading-xl">Welcome back, {user?.username}</h1>

      <div className="card">
        <div className="card-content">
          TODO
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