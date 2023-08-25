import NavBar, { NavBarColorVariant } from "@/components/navbar";
import React from "react"

export default function Page() {
  return (
    <>
      <NavBar colorVariant={NavBarColorVariant.opaque} />
      <div className="hero is-fullheight" style={{"background": "black", "display": "flex", "height": "100%"}}>
        <iframe
          src="https://maps.pcbmc.co"
          style={{"flexGrow": 1}}
        />
      </div>
    </>
  )
}