import Vue from 'vue';

// Register Vue Components
import './components/register'

// Initialise base app
const app = new Vue({
    el: '#app',
});

// Nav bar
const hamburger = document.querySelector(".hamburger");
const navLinks = document.querySelector(".nav-links");

hamburger.addEventListener("click", openDrawer);

function openDrawer() {
    hamburger.classList.toggle("active");
    navLinks.classList.toggle("active");
}

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

// Initialise legacy JS-only components
import Navigation from "./navigation/Navigation";
const navigation = new Navigation()
