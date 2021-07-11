import Vue from 'vue';
import AnnouncementFeed from "./announcements/AnnouncementFeed.vue";
import NewsBar from "./news-bar/NewsBar.vue"
import Spinner from "./spinner/Spinner.vue"

Vue.component('announcement-feed', AnnouncementFeed);
Vue.component('news-bar', NewsBar);
Vue.component('spinner', Spinner);
