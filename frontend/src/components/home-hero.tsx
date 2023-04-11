import styles from '@/components/home-hero.module.scss'

export default function HomeHero() {
    return (
        <section className={["hero is-fullheight-with-navbar", styles.background].join(" ")}>
            <div className="hero-body">
                <div className="columns">
                    <div className="column is-half-tablet">
                        Test
                    </div>
                    <div className="column is-half-tablet">
                        <p className={styles.title}>
                            We build stuff. <br />
                            Come join us!
                        </p>
                        <p className={styles.subtitle}>
                            One of the world's longest-running Minecraft servers; we're a
                            community of creative players and city builders
                        </p>
                    </div>
                </div>
            </div>
        </section>
    )
}