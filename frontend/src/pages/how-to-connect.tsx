import MainFooter from "@/components/main-footer"
import NavBar, { NavBarColorVariant } from "@/components/navbar";
import React from "react"

export default function Home() {
  return (
    <>
      <NavBar colorVariant={NavBarColorVariant.opaque} />

      <section className="section">
        <div className="columns">
          <div className="column is-one-quarter">
            First column
          </div>
          <div className="column">
            <h1 className="text-title-lg">How to Connect</h1>

            <p className="text-body-md">
              Second column
            </p>
          </div>
        </div>
      </section>

      <MainFooter />
    </>
  )
}