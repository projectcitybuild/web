<template>
    <div id="announcements">
        <div class="alert alert--warning" v-if="error">
            <h3 class="alert__header"><i class="fas fa-exclamation-circle"></i> Failed to fetch announcements</h3>
            <p class="alert__message">{{ error }}</p>
        </div>
        <div v-if="announcements.length === 0">
            <skeleton-announcement />
            <skeleton-announcement />
            <skeleton-announcement />
        </div>
        <div v-else>ho</div>

    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import * as Api from './api';
import SkeletonAnnouncement from "./SkeletonAnnouncement.vue";

export default Vue.extend( {
    name: "Announcements",
    components: {SkeletonAnnouncement},
    data() {
        return {
            announcements: [] as Api.ApiTopic[],
            error: null as String
        }
    },

    created() {
        // this.getAnnouncements();
    },

    methods: {
        async getAnnouncements(){
            Api.getAnnouncements().then((response) => {
                this.announcements = response.topic_list.topics.slice(0, 3);
            }).catch((e) => {
                this.error = e.message;
            })
        }
    }
});
</script>
