import styles from '@/components/main-footer.module.scss'
import React from "react";

export default function MainFooter() {
  return (
    <footer className={styles.footer}>
      <div className="container">
        <ul>
          <li>
            <a href="">Terms of Service</a>
          </li>
          <li>
            <a href="">Privacy Policy</a>
          </li>
        </ul>
      </div>
    </footer>
  )
}