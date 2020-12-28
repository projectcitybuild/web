<template>
    <article class="article card">
        <div class="article__container">
            <div class="article__heading">
                <div class="skeleton skeleton--dark skeleton--large"
                     :style="{width: getRandom(40, 80) + '%'}"></div>
            </div>
            <div class="article__date">
                <div class="skeleton" style="width:20%"></div>
            </div>
            <div class="article__body">
                <div v-for="i in numLines"
                     class="skeleton"
                     :style="{width: getRandom(60, 100) + '%'}"> </div>
            </div>
            </div>

            <div class="article__author">
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
                            <span class="stat__figure">
                                <div class="skeleton skeleton--medium skeleton--dark skeleton--middle"
                                     style="width:15px"></div>
                            </span>
                    <span class="stat__heading">
                                <div class="skeleton skeleton--small" style="width:50px"></div>
                            </span>
                </div>
                <div class="stat">
                            <span class="stat__figure">
                                <div class="skeleton skeleton--medium skeleton--dark skeleton--middle"
                                     style="width:15px"></div>
                            </span>
                    <span class="stat__heading">
                                <div class="skeleton skeleton--small" style="width:50px"></div>
                            </span>
                </div>
            </div>
            <div class="article__actions">
                <div class="skeleton skeleton--button"></div>
            </div>
        </div>
    </article>
</template>

<script lang="ts">
export default {
    name: "SkeletonAnnouncement",

    data() {
        return {
            numLines: 0
        }
    },

    created() {
        this.numLines = this.getRandom(3, 6);
    },

    methods: {
        getRandom(min: number, max: number): number {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min) + min);
        }

    }
};
</script>
