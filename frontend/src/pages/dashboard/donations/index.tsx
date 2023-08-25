import DashboardLayout from "@/components/layouts/dashboard-layout"
import withAuth from "@/hooks/withAuth"
import { GetStaticProps, NextPage } from "next"
import React from "react";

const Page: NextPage = (props): JSX.Element => {
  return (
    <DashboardLayout>
      <h1 className="text-heading-xl">Donation Perks</h1>

      <div className="card">
        <table className="table is-striped is-fullwidth">
          <thead>
            <tr>
              <th>Status</th>
              <th>Tier</th>
              <th>Start Date</th>
              <th>Expiry Date</th>
              <th>Gifter</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <span className="tag is-success">Active</span>
              </td>
              <td>Iron Tier</td>
              <td>Aug 5th, 2023</td>
              <td>Sep 5th, 2023</td>
              <td>-</td>
            </tr>
            <tr>
              <td>
                <span className="tag is-dark">Expired</span>
              </td>
              <td>Iron Tier</td>
              <td>Aug 5th, 2023</td>
              <td>Sep 5th, 2023</td>
              <td>-</td>
            </tr>
          </tbody>
        </table>
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