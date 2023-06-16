import HomeHero from "@/components/home-hero";
import NavBar, { NavBarColorVariant, NavBarMenuSet } from "@/components/navbar";
import React from "react"

export default function Home() {
  return (
    <>
        <NavBar
          menuSet={NavBarMenuSet.home}
          colorVariant={NavBarColorVariant.floatingTransparent}
        />
        <HomeHero />

        <section className="section">
            <div className="reverse-columns-mobile">
                <div className="column">
                    test
                </div>
                <div className="column">
                    test
                </div>
            </div>
        </section>
    </>
  )
}