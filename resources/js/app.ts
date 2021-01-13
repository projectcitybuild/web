import Vue from 'vue';

// Register Vue Components
import './components/register'

// Initialise base app
const app = new Vue({
    el: '#app',
});

// Initialise legacy JS-only components
import Navigation from "./navigation/Navigation";
const navigation = new Navigation()
