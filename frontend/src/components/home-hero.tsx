import styles from '@/components/home-hero.module.scss'
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faArrowPointer } from "@fortawesome/free-solid-svg-icons";
import { Routes } from "@/constants/routes";
import React from "react";
import Link from "next/link";

export default function HomeHero() {
    return (
        <section className={`hero is-fullheight ${styles.hero} ${styles.background}`}>
            <div className="hero-body">
                <div className={`columns`}>
                    <div className={`column is-half-tablet ${styles.columnText}`}>
                        <p className={styles.title}>
                            We build stuff. <wbr />
                            Come join us!
                        </p>
                        <p className={styles.subtitle}>
                            One of the world's longest-running Minecraft servers; we're a <wbr />
                            community of creative players and city builders
                        </p>
                        <Link href={Routes.REGISTER} className={styles.button}>
                            <FontAwesomeIcon icon={faArrowPointer} /> Connect to pcbmc.co
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    )
}