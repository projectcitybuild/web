import styles from '@/components/server-feed.module.scss'
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"
import { faCopy, faUser } from '@fortawesome/free-solid-svg-icons'
import { faDiscord } from "@fortawesome/free-brands-svg-icons"

export default function ServerFeed() {
    return (
        <div className={styles.servers}>
            <div className={styles.server}>
                <div className={styles.title}>Minecraft (Java)</div>
                <div className={styles.playerCount}>
                    <FontAwesomeIcon icon={faUser} />
                    <span>1 / 40</span>
                </div>
                <div className={styles.address}>
                    <a href="" data-server-address="">
                        <FontAwesomeIcon icon={faCopy} />
                        pcbmc.co
                    </a>
                </div>
            </div>

            <div className={styles.server}>
                <span className={`icon-text ${styles.title}`}>
                    <span className="icon">
                      <FontAwesomeIcon icon={faDiscord} />
                    </span>
                    <span>Discord</span>
                </span>
                <span className={styles.address}>
                    <a href="https://discord.gg/3NYaUeScDX" target="_blank" rel="noopener noreferrer">Connect / Open</a>
                </span>
            </div>
        </div>
    )
}