import NavBar from "@/components/navbar";
import HomeHero from "@/components/home-hero";

export default function Home() {
  return (
    <>
        <NavBar />
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