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

hamburger.addEventListener("click", mobileMenu);

function mobileMenu() {
    hamburger.classList.toggle("active");
    navLinks.classList.toggle("active");
}

// Initialise legacy JS-only components
import Navigation from "./navigation/Navigation";
const navigation = new Navigation()
