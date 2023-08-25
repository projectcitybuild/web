import HomeHero from "@/components/home-hero";
import NavBar, { NavBarColorVariant } from "@/components/navbar";
import React from "react"

export default function Home() {
  return (
    <>
        <NavBar colorVariant={NavBarColorVariant.floatingTransparent} />

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