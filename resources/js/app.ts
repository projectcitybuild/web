import Vue from 'vue';
import axios from 'axios';

import Announcements from "./components/announcements/Announcements.vue";

Vue.component('announcements', Announcements);

const app = new Vue({
    el: '#app',
});
