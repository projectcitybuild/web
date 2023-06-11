import styles from '@/components/home-hero.module.scss'
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faArrowPointer } from "@fortawesome/free-solid-svg-icons";
import { faDiscord } from "@fortawesome/free-brands-svg-icons";
import { Routes } from "@/constants/routes";
import React from "react";
import Link from "next/link";

export default function HomeHero() {
    return (
        <section className={`hero is-fullheight ${styles.background}`}>
            <div className={`hero-body ${styles.layoutHeroBody}`}>
                <div className={styles.layoutRightCol}>
                    <div className={styles.layoutTitle}>
                        <h1 className="text-display-lg">
                            We build stuff. <wbr />
                            Come join us!
                        </h1>
                    </div>
                    <div className={styles.layoutDescription}>
                        <span className="text-title-sm">
                            One of the <strong>world's longest-running</strong> Minecraft servers; we're a <wbr />
                            community of creative players and city builders
                        </span>
                    </div>
                    <div className={styles.layoutButton}>
                        <Link href={Routes.REGISTER} className={styles.button}>
                            <FontAwesomeIcon icon={faArrowPointer} /> Connect to <u>pcbmc.co</u>
                        </Link>
                    </div>
                    <div>
                        <a href="https://discord.gg/3NYaUeScDX" className={styles.discordLink}>
                            <FontAwesomeIcon icon={faDiscord} /> Join our Discord
                        </a>
                    </div>
                </div>
            </div>
        </section>
    )
}