import styles from '@/components/home-hero.module.scss'
import ServerFeed from "@/components/server-feed";

export default function HomeHero() {
    return (
        <section className={["hero is-fullheight-with-navbar", styles.background].join(" ")}>
            <div className="hero-body">
                <div className={["columns", styles.reverseColumnsMobile].join(" ")}>
                    <div className="column is-half-tablet">
                        <ServerFeed />
                    </div>
                    <div className="column is-half-tablet">
                        <p className={styles.title}>
                            We build stuff. <wbr />
                            Come join us!
                        </p>
                        <p className={styles.subtitle}>
                            One of the world's longest-running Minecraft servers; we're a <wbr />
                            community of creative players and city builders
                        </p>
                    </div>
                </div>
            </div>
        </section>
    )
}