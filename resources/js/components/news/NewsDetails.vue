<template>
    <v-row justify="center">
        <custom-dialog :dialog="inputData" @close="closePopover">
            <template v-slot:title>
                News vom {{ news.updatedAt }}
            </template>
            <template v-slot:content>
                <v-card-title class="news-title">
                    <span class="text-h5">{{news.title}}</span>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="ckeditor-html news-content" v-html="news.content">
                </v-card-text>
            </template>
        </custom-dialog>
    </v-row>
</template>

<script>
import News from "../../models/news"
import CustomDialog from "../dialog/CustomDialog"

export default {
    name: "NewsDetails",
    components: {CustomDialog},
    props: {
        inputData: Boolean,
        news: {
            type: News,
            required: true
        },
    },
    methods: {
        closePopover() {
            this.$emit('update:inputData', false)
        },
    }
}
</script>

<style scoped>
.news-title {
  flex-shrink: 0;
}
.news-content {
  flex-grow: 1;
  overflow-y: auto;
}
.news-actions {
  flex-shrink: 0;
}
</style>
