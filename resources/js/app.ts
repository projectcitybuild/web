import Vue from 'vue';

// Register Vue Components
import './components/register'

// Initialise base app
const app = new Vue({
    el: '#app',
});

// Nav bar
const hamburger = document.querySelector(".hamburger")
const navLinks = document.querySelector(".nav-links")

hamburger.addEventListener("click", openDrawer)

function openDrawer() {
    hamburger.classList.toggle("active")
    navLinks.classList.toggle("active")
}

const dropdownLinks = document.querySelectorAll(".nav-dropdown")

dropdownLinks.forEach(node => {
    node.addEventListener("click", () => {
        console.log(node.classList)

        // Collapse every other submenu first
        // FIXME: optimize this later
        dropdownLinks.forEach(otherNode => {
            if (!node.isEqualNode(otherNode)) {
                otherNode.nextElementSibling.classList.remove("active")
            }
        })

        let menu = node.nextElementSibling
        menu.classList.toggle("active")
    })
})

// Server feed copy-to-clipboard
document.querySelectorAll(`[data-server-address]`).forEach(element => {
    let value = element.getAttribute("data-server-address")
    element.addEventListener("click", () => copyToClipboard(value))
})

function copyToClipboard(text: string) {
    navigator.clipboard.writeText(text)
        .then(() => {
            alert('Copied to clipboard')
        })
}

// Background image rotator
const heroElement = document.getElementById('hero')
if (heroElement) {
    const choices = [
        "bg-1",
        "bg-3",
        "bg-4",
    ]
    const roll = Math.floor(Math.random() * (choices.length))
    heroElement.classList.add(choices[roll])
}

// Initialise legacy JS-only components
import Navigation from "./navigation/Navigation";
const navigation = new Navigation()
