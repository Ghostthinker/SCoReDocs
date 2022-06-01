<template>
    <div>
        <v-btn color="primary" dark @click="showModal=true">Upload Video</v-btn>
        <media-form :inputData.sync="showModal" v-on:upload-completed="reload"></media-form>
        <magic-grid>

            <v-card
                v-for="n in this.laravelData"
                :key="n.id"
                class="pa-2"
                outlined

                v-on:click="showDialog(n)">


                <v-img
                    v-if="n.previewURL"
                    class="white--text align-end"
                    height="200px"
                    :src="n.previewURL"
                ></v-img>
                <v-card-title v-if="n.caption">{{n.caption}}</v-card-title>
                <v-card-title v-else>Media  {{n.id}}</v-card-title>
                <v-card-subtitle>{{n.created_at}}</v-card-subtitle>
                <v-card-text>
                    <v-label>{{n.fileName}}</v-label>
                </v-card-text>
            </v-card>
        </magic-grid>
        <v-row justify="center">
            <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">

                <v-card>
                    <v-toolbar dark color="primary">

                        <v-toolbar-title>{{activeMedia.streamingURL_720p}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-toolbar-items>
                            <v-btn icon dark @click="dialog = false">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </v-toolbar-items>
                    </v-toolbar>
                    <media-player v-if="dialog" :media_id="activeMedia.id">

                    </media-player>
                </v-card>
            </v-dialog>
        </v-row>
    </div>
</template>

<script>
import MediaPlayer from "./MediaPlayer"
import MediaForm from "./MediaForm"

export default {
    name: "MediaDashboard",
    components: {MediaForm, MediaPlayer},
    data() {
        return {
            laravelData: {},
            dialog: false,
            showModal: false,
            activeMedia: {
                id: null,
                streamingURL_720p: null
            }
        }
    },
    mounted() {
        this.getData()
    },

    methods: {
        getData() {
            axios.get('/rest/media')
                .then(response => {
                    this.laravelData = response.data

                    setTimeout(() => {
                        window.dispatchEvent(new Event('resize'))
                        console.log("resize")
                    }, 100)
                })
        },
        reload() {
            this.getData()
        },
        showDialog(media) {
            console.log("active dialog")
            console.table(media)
            this.activeMedia = media
            this.dialog = true
        }
    }
}
</script>

<style scoped>
</style>
