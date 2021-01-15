<template>
    <article class="article card">
        <div class="article__container">
            <h2 class="article__heading">{{ topic.title }}</h2>
            <div class="article__date">{{ date }}</div>

            <div class="article__body" v-html="content" v-if="topic.details"></div>
            <div class="article__body" v-else>
                <div class="spinner">
                    <div class="rect1"></div>
                    <div class="rect2"></div>
                    <div class="rect3"></div>
                    <div class="rect4"></div>
                    <div class="rect5"></div>
                </div>
            </div>
            <div class="article__author" v-if="topic.details">
                Posted by
                <img :src="avatarUrl" width="16" :alt="`${topic.details.details.created_by.username}'s Avatar`"/>
                <a :href="`https://forums.projectcitybuild.com/u/${topic.details.details.created_by.username}`">{{ topic.details.details.created_by.username }}</a>
            </div>
            <div class="article__author" v-else>
                <div class="skeleton-row">
                    <div class="skeleton" style="width:200px"></div>
                    <div class="skeleton skeleton--square skeleton--dark"></div>
                    <div class="skeleton"></div>
                </div>
            </div>
        </div>
        <div class="article__footer">
            <div class="stats-container">
                <div class="stat">
                    <span class="stat__figure">{{ topic.posts_count - 1 }}</span>
                    <span class="stat__heading">Comments</span>
                </div>
                <div class="stat">
                    <span class="stat__figure">{{ topic.views }}</span>
                    <span class="stat__heading">Post Views</span>
                </div>
            </div>
            <div class="actions">
                <a class="button button--accent button--large"
                   :href="`https://forums.projectcitybuild.com/t/${topic.slug}/${topic.id}`">
                    Read Post
                    <i class="fa fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </article>
</template>

<script lang="ts">
import Vue, {PropType} from "vue";
import * as Api from './api';
import {ApiPost} from "./api";

export default Vue.extend({
    name: "Announcement",

    props: {
        topic_id: Number
    },

    data() {
        return {
            topic: null as Api.ApiTopic
        }
    },

    created() {
        Api.getTopic(this.topic_id).then((topic) => this.topic = topic)
    },

    computed: {
        initialPost(): ApiPost {
            return this.topic.details.post_stream[0];
        },

        content(): string {
            let content = this.initialPost.cooked;
            content.replace(/href="\//g, 'href="https://forums.projectcitybuild.com/');
            return content;
        },

        avatarUrl() {
            return "https://forums.projectcitybuild.com" + this.initialPost.avatar_template.replace('{size}', '16');
        }
    }
});
</script>
