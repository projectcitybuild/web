import NavBar, { NavBarVariant } from "@/components/navbar";
import React from "react"

export default function Page() {
  return (
    <>
      <NavBar variant={NavBarVariant.home} />

      <div className="hero is-fullheight">
        <div className="hero-body p-0" style={{"background": "black"}}>
          <iframe
            height="100%"
            width="100%"
            src="https://maps.pcbmc.co"
          />
        </div>
      </div>
    </>
  )
}