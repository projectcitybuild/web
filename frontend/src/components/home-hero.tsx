import styles from '@/components/home-hero.module.scss'
import { Routes } from "@/constants/Routes";
import React from "react";
import Link from "next/link";
import Icon, { IconToken } from "@/components/icon";

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
              <Icon token={IconToken.arrowPointer} /> Connect to <u>pcbmc.co</u>
            </Link>
          </div>
          <div>
            <a href="https://discord.gg/3NYaUeScDX" className={styles.discordLink}>
              <Icon token={IconToken.discord} /> Join our Discord
            </a>
          </div>
        </div>
      </div>
    </section>
  )
}