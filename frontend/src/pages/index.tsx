import HomeHero from "@/components/home-hero";
import NavBar, { NavBarVariant } from "@/components/navbar";

export default function Home() {
  return (
    <>
        <NavBar variant={NavBarVariant.home} />
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