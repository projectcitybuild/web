import HomeHero from "@/components/home-hero";
import MainFooter from "@/components/main-footer"
import NavBar, { NavBarColorVariant } from "@/components/navbar";
import React from "react"

export default function Home() {
  return (
    <>
        <NavBar colorVariant={NavBarColorVariant.floatingTransparent} />

        <HomeHero />

        <section className="section">
            <div className="columns">
                <div className="column">
                    <h1 className="text-display-md">Minecraft 24/7</h1>

                    <p className="text-body-md">
                      We're a Minecraft community that's been around <strong>since 2010</strong>.
                      <br /><br />
                      With our free-build Creative and Survival multiplayer maps, we offer a fun platform & building
                      experience like no other. You can visit and build in established towns & cities or start your own.
                    </p>
                </div>
                <div className="column">
                    test
                </div>
            </div>
        </section>

        <MainFooter />
    </>
  )
}