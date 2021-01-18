<template>
    <div id="announcements">
        <div v-if="error" class="alert alert--warning" >
            <h3 class="alert__header"><i class="fas fa-exclamation-circle"></i> Failed to fetch announcements</h3>
            <p class="alert__message">{{ error }}</p>
        </div>
        <div v-else-if="announcements.length === 0">
            <skeleton-announcement/>
            <skeleton-announcement/>
            <skeleton-announcement/>
        </div>
        <div v-else>
            <announcement v-for="topic in announcements" :topic_id="topic.id" :key="topic.id"/>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import * as Api from './api';
import SkeletonAnnouncement from "./SkeletonAnnouncement.vue";
import Announcement from "./Announcement.vue";

export default Vue.extend({
    name: "AnnouncementFeed",
    components: {Announcement, SkeletonAnnouncement},
    data() {
        return {
            announcements: [] as Api.DiscourseTopic[],
            error: null as String
        }
    },

    created() {
        this.getAnnouncements();
    },

    methods: {
        async getAnnouncements() {
            Api.getAnnouncements().then((response) => {
                this.announcements = response.topic_list.topics.slice(0, 3);
            }).catch((e) => {
                this.error = e.message;
            })
        }
    }
});
</script>
