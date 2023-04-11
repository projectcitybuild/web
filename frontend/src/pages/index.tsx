import styles from '@/styles/Home.module.css'
import NavBar from "@/components/navbar";
import HomeHero from "@/components/home-hero";

export default function Home() {
  return (
    <>
        <NavBar />
        <HomeHero />

        <section className="section">
            Test
        </section>
    </>
  )
}