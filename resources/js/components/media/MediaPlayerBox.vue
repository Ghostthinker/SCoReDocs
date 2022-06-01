<template>
    <div v-if="showPlayer">
        <div
            :class="minified ? 'minified' : 'v-dialog v-dialog--active v-dialog--fullscreen fullscreen'" hide-overlay
            transition="dialog-bottom-transition">
            <v-card>
                <v-toolbar dark color="primary">
                    <v-toolbar-title></v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn icon dark @click="onResizeButtonClick()">
                            <v-icon v-if="minified">mdi-arrow-expand</v-icon>
                            <v-icon v-if="!minified">mdi-arrow-collapse</v-icon>
                        </v-btn>
                        <v-btn icon dark @click="closePlayer()">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-progress-linear
                    indeterminate
                    :active="!isPlayerReady"
                    color="blue"
                    class="v-progress-linear--absolute progressbar-media"
                ></v-progress-linear>
                <div :class="(!isPlayerReady)  ? 'ep5-loading-background' : ''">
                    <media-player
                        v-if="showEp5"
                        :showPlayer="showEp5"
                        :media_id="playerMediaId"
                        :inputData.sync="minified"
                        :annotation_id="playerAnnotationId"
                        :sequence_id="playerSequenceId"
                        :playListData="playerPlayListData"
                        :playlistPosition="playlistPosition"
                        :isPlayerReady="isPlayerReady"
                        @reloadPlayerForPlaylist="onReloadPlayerForPlaylist"
                        @reloadPlayerForVideo="reloadPlayerForVideo"
                        @onPlayerDestroy="onPlayerDestroy"
                        @noVideoPlayer="onNoVideoPlayer"
                    ></media-player>
                </div>
            </v-card>
        </div>
    </div>
</template>

<script>
import MediaPlayer from "./MediaPlayer"
import store from "../../store";

export default {
    name: "MediaPlayerBox",
    components: {
        MediaPlayer
    },
    props: [
        'showPlayer',
        'activeMediaId',
        'annotationId',
        'sequenceId',
        'playListData'
    ],
    data() {
        return {
            minified: false,
            showEp5: this.showPlayer,
            playerMediaId: this.activeMediaId,
            playerAnnotationId: this.annotationId,
            playerSequenceId: this.sequenceId,
            playerPlayListData: this.playListData,
            loading: false,
            showPlayerCanvas: false,
            playlistPosition: 0,
            isPlayerReady: false
        }
    },
    computed: {
        isShowEp5(){
            if(this.showEp5){
                return this.showPlayer
            }
            return false
        }
    },
    methods: {
        onResizeButtonClick: function () {
            this.minified = !this.minified
        },
        closePlayer: function () {
            this.playlistPosition = 0
            this.$emit('closePlayer')
        },
        onReloadPlayerForPlaylist(payload) {
            this.loading = true

            this.showEp5 = false
            this.isPlayerReady = false

            let playlist = payload.playlist
            let position = payload.position
            this.playlistPosition = position
            if(playlist[position]){
                let media = playlist[position]
                if(media.type === 'Video'){
                    this.playerMediaId = media.id
                    this.playerSequenceId = null
                    this.playerAnnotationId = null
                }else if(media.type === 'Sequence'){
                    this.playerMediaId = media.video_nid
                    this.playerSequenceId = media.id
                    this.playerAnnotationId = null
                }
            }

            setTimeout(() => {
                this.showEp5 = true
            }, 50)

        },
        reloadPlayerForVideo(payload) {
            this.loading = true

            this.showEp5 = false
            this.isPlayerReady = false

            this.playerPlayListData = null
            this.playerSequenceId = null
            this.playerMediaId = payload.video_nid

            setTimeout(() => {
                this.showEp5 = true
            }, 50)
        },
        onPlayerDestroy(){
            this.isPlayerReady = false
            this.loading = false
        },
        onNoVideoPlayer() {
            this.closePlayer()
            this.$store.dispatch('notifications/persistentNotification', {
                message: 'Es konnte kein Video Player gefunden werden',
                type: 'error'
            })
        }
    },
    watch: {
        'minified': function () {
            setTimeout(function () {
                window.dispatchEvent(new Event('resize'))
                console.log("resize")
            }, 250)
        },
        'showPlayer': function () {
            this.showEp5 = this.showPlayer
            if(this.showPlayer){
                this.loading = true
            }
        },
        'activeMediaId': function () {
            this.playerMediaId = this.activeMediaId
        },
        'annotationId': function () {
            this.playerAnnotationId = this.annotationId
        },
        'sequenceId': function () {
            this.playerSequenceId = this.sequenceId
        },
        'playListData': function () {
            this.playerPlayListData = this.playListData
            this.playlistPosition = 0
        }
    },
    created(){
        this.loading = true
        window.addEventListener('edubreakplayer-plugins-loaded', () => {
            this.isPlayerReady = true
            this.showPlayerCanvas = false
            this.loading = false
        })
    }
}
</script>

<style lang="scss">

.progressbar-media{
    z-index: 999999;
}

.ep5-loading-background {
    margin: 0 auto;
    height: calc(100vh - 64px);
    min-height: calc(100vh - 64px) !important;
    background-color: #333333;
}

.edubreakplayer {
    margin: 0 auto;
    height: calc(100vh - 64px);
    min-height: calc(100vh - 64px) !important;
}

.fullscreen {
    z-index: 9999999;
}

.hide-canvas{
    display: none;
}

.block-canvas{
    display: block;
}

.minified {
    width: 350px;
    margin: 0 1em 8em;
    z-index: 1000;

    .v-card {
        width: 350px;
        border-radius: 9px;
    }

    .ep5-loading-background {
        width: 350px !important;
        min-width: 350px !important;
        min-height: 275px !important;
        height: 275px !important;
    }

    .edubreakplayer {
        width: 350px !important;
        min-width: 350px !important;
        min-height: 275px !important;
        height: 275px !important;
        border-bottom-left-radius: 9px;
        border-bottom-right-radius: 9px;
    }

    .ep5-sidebar {
        display: none !important;
    }

    .ep5-controlbar {
        width: 350px !important;
    }

    .ep5-media {
        width: 350px !important;
        top: 0px !important;
    }

    .ep5-sidebar-overlay-buttons {
        display: none !important;
    }

    .ep5-playbackrate-control {
        display: none !important;
    }

    .secondary-control {
        display: none !important;
    }
}
</style>
