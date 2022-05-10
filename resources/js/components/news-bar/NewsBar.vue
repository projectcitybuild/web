<template>
    <section class="news-bar">

        <div class="container" v-if="isLoading">
            <span class="news-bar__date">
                <i class="fas fa-calendar-day"></i> Loading...
            </span>
            <div class="news-bar__spinner">
                <spinner></spinner>
            </div>
            <a class="news-bar__view-more" href="https://forums.projectcitybuild.com/c/announcements/5"><i class="fas fa-chevron-right"></i> All Announcements</a>
        </div>

        <div class="container" v-if="announcement && isLoaded">
            <span class="news-bar__date">
                <i class="fas fa-calendar-day"></i> {{ date }}
            </span>

            <a class="news-bar__title" :href="`https://forums.projectcitybuild.com/t/${announcement.slug}/${announcement.id}`">{{ announcement.title }}</a>
            <a class="news-bar__view-more" href="https://forums.projectcitybuild.com/c/announcements/5"><i class="fas fa-chevron-right"></i> All Announcements</a>
        </div>

        <div class="container" v-if="isLoadedEmpty">
            <span class="news-bar__title defocussed">No Announcement Found</span>
            <a class="news-bar__view-more" href="https://forums.projectcitybuild.com/c/announcements/5"><i class="fas fa-chevron-right"></i> All Announcements</a>
        </div>

        <div class="container" v-if="isError">
            <span class="news-bar__title defocussed">Failed to load announcements. <a href="javascript:void(0)" @click="retryFetch">Try again</a>?</span>
            <a class="news-bar__view-more" href="https://forums.projectcitybuild.com/c/announcements/5"><i class="fas fa-chevron-right"></i> All Announcements</a>
        </div>

    </section>
</template>

<script lang="ts">
import Vue from "vue";
import * as Api from "./api";
import { format, parseISO } from "date-fns";

const ViewState = Object.freeze({
    Loading: 1,
    Loaded: 2,
    LoadedEmpty: 3, // No announcement found
    Error: 4,
})

export default Vue.extend({
    name: "NewsBar",

    data() {
        return {
            state: ViewState.Loading,
            announcement: null as Api.DiscourseTopic,
            error: null as String
        }
    },

    created() {
        this.fetchAnnouncements()
    },

    methods: {
        async fetchAnnouncements() {
            this.state = ViewState.Loading
            this.announcement = null

            Api.getAnnouncements().then((response) => {
                this.announcement = response.topic_list.topics[0]
                if (this.announcement) {
                    this.state = ViewState.Loaded
                } else {
                    this.state = ViewState.LoadedEmpty
                }
            }).catch((error) => {
                this.error = error.message
                this.state = ViewState.Error
            })
        },
        retryFetch() {
            this.fetchAnnouncements()
        },
    },

    computed: {
        date(): string {
            const date = parseISO(this.announcement.created_at)
            return format(date, 'EEE, MMM do, yyyy')
        },
        isLoading(): boolean {
            return this.state == ViewState.Loading
        },
        isLoaded(): boolean {
            return this.state == ViewState.Loaded
        },
        isLoadedEmpty(): boolean {
            return this.state == ViewState.LoadedEmpty
        },
        isError(): boolean {
            return this.state == ViewState.Error
        },
    }
});
</script>
