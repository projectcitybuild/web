import styles from '@/components/home-hero.module.scss'
import ServerFeed from "@/components/server-feed";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faArrowPointer} from "@fortawesome/free-solid-svg-icons";

export default function HomeHero() {
    return (
        <section className={`hero is-fullheight-with-navbar ${styles.hero} ${styles.background}`}>
            <div className="hero-body">
                <div className={`columns ${styles.reverseColumnsMobile}`}>
                    <div className={`column is-half-tablet ${styles.columnServerFeed}`}>
                        <ServerFeed />
                    </div>
                    <div className={`column is-half-tablet ${styles.columnText}`}>
                        <p className={styles.title}>
                            We build stuff. <wbr />
                            Come join us!
                        </p>
                        <p className={styles.subtitle}>
                            One of the world's longest-running Minecraft servers; we're a <wbr />
                            community of creative players and city builders
                        </p>
                        <a href="" className={styles.button}>
                            <FontAwesomeIcon icon={faArrowPointer} /> Register
                        </a>
                    </div>
                </div>
            </div>
        </section>
    )
}