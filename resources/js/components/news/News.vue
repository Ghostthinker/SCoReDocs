<template>
    <div>
        <v-card
            v-for="item of getNews"
            :key="item.id"
            height="57"
            class="my-2 news-card"
            v-bind:class="{ 'read': item.read }"
            v-on:click="openNews(item)"
        >
            <v-list-item>
                <v-list-item-content>
                    <v-list-item-title>
                        <span class="news-updated" v-if="newsWasUpdated(item)">
                            !
                        </span>
                        <span class="news-date">
                            {{item.updatedAt}}
                        </span>
                        <span class="news-sep">
                            |
                        </span>
                        <span class="news-title">
                            {{item.title}}
                        </span>
                    </v-list-item-title>
                </v-list-item-content>
                <v-list-item-action>
                    <v-btn
                        icon
                        class="edit-btn"
                        v-if="getCanEditPermission"
                        v-on:click="editNews($event, item)"
                    >
                        <v-icon
                            color="primary"
                            small
                            class="edit-icon"
                        >
                            mdi-pencil
                        </v-icon>
                    </v-btn>
                </v-list-item-action>
            </v-list-item>
        </v-card>
        <news-details
            v-if="selectedNews"
            :news="selectedNews"
            :inputData.sync="showNewsDetails"
        >
        </news-details>
        <news-form :inputData.sync="showEditNews"
                   v-if="showEditNews"
                   :news-to-edit="newsToEdit"
                   :headline="'News bearbeiten'"
                   :success-message="'Bearbeiten der News war erfolgreich'"
                   :generic-error-message="'Es gab einen Fehler beim Erstellen einer News'"
                   :not-allowed-message="'Nur das SCoRe-Team und Lernbegleiter können eine News erstellen'"
        ></news-form>
    </div>
</template>

<script>

import {createNamespacedHelpers} from "vuex"
import NewsDetails from "./NewsDetails"
import News from "../../models/news"
import NewsForm from "./NewsForm"
const {mapGetters} = createNamespacedHelpers('news')
const {mapActions} = createNamespacedHelpers('news')

export default {
    name: "News",
    components: {NewsForm, NewsDetails},
    data: () => ({
        selectedNews: null,
        showNewsDetails: false,
        showEditNews: false,
        newsToEdit: null
    }),
    computed: {
        ...mapGetters(['getNews', 'getCanEditPermission'])
    },
    methods: {
        ...mapActions(['pushNews', 'readNews', 'updateNews', 'removeNews']),
        openNews(news) {
            if (!news.read) {
                this.readNews(news)
                news.read = true
            }
            this.selectedNews = news
            this.showNewsDetails = true
        },
        editNews(event, news) {
            event.stopPropagation()
            this.newsToEdit = news
            this.showEditNews = true
        },
        newsWasUpdated(news) {
            return news.createdAtTimestamp !== news.updatedAtTimestamp && !news.read
        },
        initEcho() {
            Echo.channel('news')
                .listen('.newNews', (data) => {
                    this.pushNews(News.create(data.news))
                })
                .listen('.updateNews', (data) => {
                    this.updateNews(News.create(data.news))
                })
                .listen('.deleteNews', (data) => {
                    this.removeNews(data.news_id)
                })
        }
    },
    mounted() {
        this.$store.dispatch('news/fetchNews')
    },
    created() {
        this.initEcho()
    }
}
</script>

<style lang="scss" scoped>

.news-updated {
  color: var(--v-primary-base)
}

.news-date {
    color: var(--v-primary-base)
}

.edit-btn {
  outline: none !important;
}

.news-card.read {
  opacity: 0.5;
}

</style>
