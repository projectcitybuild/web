import { Inter } from 'next/font/google'
import styles from '@/styles/Home.module.css'

export default function Home() {
  return (
    <>
        <h1>Home</h1>
        <ul>
            <li><a href="/login">Login</a></li>
        </ul>
    </>
  )
}