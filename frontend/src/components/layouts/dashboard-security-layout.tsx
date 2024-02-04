import DashboardTabBar from "@/components/dashboard-tab-bar"
import DashboardLayout from "@/components/layouts/dashboard-layout"
import React, { ReactElement } from "react";

type Props = {
  children: ReactElement|ReactElement[],
}

export default function DashboardSecurityLayout(props: Props) {
  return (
    <DashboardLayout>
      <h1 className="text-heading-xl">Account Settings</h1>

      <DashboardTabBar />

      <div className="card">
        {props.children}
      </div>
    </DashboardLayout>
  )
}