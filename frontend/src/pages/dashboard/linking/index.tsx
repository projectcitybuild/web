import DashboardLayout from "@/components/layouts/dashboard-layout"
import withAuth from "@/hooks/withAuth"
import { GetStaticProps, NextPage } from "next"
import React from "react";

const Page: NextPage = (props): JSX.Element => {
  return (
    <DashboardLayout>
      <h1 className="text-heading-xl">Linked Accounts</h1>

      <div className="card">
        <div>
          Minecraft
        </div>

        

        <div>
          Discord
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