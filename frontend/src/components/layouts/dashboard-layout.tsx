import DashboardNavBar from "@/components/dashboard-navbar"
import styles from "@/components/layouts/dashboard-layout.module.scss";
import React, { ReactElement } from "react";

type Props = {
  children: ReactElement|ReactElement[],
}

export default function DashboardLayout(props: Props) {
  return (
    <>
      <DashboardNavBar />

      <main className={styles.contents}>
        <div className="container">
          {props.children}
        </div>
      </main>
    </>
  )
}