<template>
    <div>
        <div class="edubreakplayer" id="edubreakplayer"
             data-nid="12"
        >
            <div class="ep5-media">
                <video id="videoElement" preload="auto" class="ep5-media-video" ref="video" crossorigin="anonymous"
                       @canplay="updatePaused" @play="onPlaying" @pause="onPause" @seeked="onSeek" @ended="onEnd">
                    <source
                        :src="videoSource"
                        type="video/mp4"
                    />
                    <div>'.t("Sorry, your browser or device is not
                        supported!").'
                    </div>
                </video>
            </div>
        </div>
        <custom-dialog :dialog="show" show-abort-button @close="show = false">
            <template v-slot:title>
                Kommentarverlinkung einfügen?
            </template>
            <template v-slot:content>
                <v-checkbox
                    v-model="enableThumbnail"
                    label="Mit Thumbnail"
                ></v-checkbox>
            </template>
            <template v-slot:actions>
                <v-btn color="primary" @click="insertAnnotation(enableThumbnail)">Einfügen</v-btn>
            </template>
        </custom-dialog>
        <custom-dialog
            :dialog="showSequenceShareDialog"
            :loading="loadingSequence"
            show-abort-button
            @close="showSequenceShareDialog = false"
        >
            <template v-slot:title>
                Sequenzverlinkung einfügen?
            </template>
            <template v-slot:content>
                <v-checkbox
                    v-model="enableSequenceThumbnail"
                    label="Mit Thumbnail"
                ></v-checkbox>
            </template>
            <template v-slot:actions>
                <v-btn color="primary" @click="insertSequence(enableSequenceThumbnail)">Einfügen</v-btn>
            </template>
        </custom-dialog>
        <custom-dialog :dialog="showSequenceShareDialog" show-abort-button @close="showSequenceShareDialog = false">
            <template v-slot:title>
                Sequenzverlinkung einfügen?
            </template>
            <template v-slot:content>
                <v-checkbox
                    v-model="enableSequenceThumbnail"
                    label="Mit Thumbnail"
                ></v-checkbox>
            </template>
            <template v-slot:actions>
                <v-btn color="primary" @click="insertSequence(enableSequenceThumbnail)">Einfügen</v-btn>
            </template>
        </custom-dialog>
        <custom-dialog :dialog="showVideoShareDialog" show-abort-button @close="showVideoShareDialog = false">
            <template v-slot:title>
                Videoverlinkung einfügen?
            </template>
            <template v-slot:actions>
                <v-btn color="primary" @click="insertVideo()">Einfügen</v-btn>
            </template>
        </custom-dialog>
    </div>
</template>

<script>
import axios from "axios"
import CustomDialog from "../dialog/CustomDialog"

export default {
    name: "MediaPlayer",
    components: {CustomDialog},
    props: ['media_id', 'annotation_id', 'sequence_id', 'inputData', 'playListData', 'playlistPosition', 'showPlayer', 'isPlayerReady'],

    data() {
        return {
            baseXapiUri: '/rest/xapi/',
            projectId: 0,
            sectionId: 0,
            // Our data object that holds the Laravel paginator data
            item: {},
            videoSource: '',
            show: false,
            showSequenceShareDialog: false,
            showVideoShareDialog: false,
            enableThumbnail: false,
            enableSequenceThumbnail: false,
            annotation: this.annotation_id,
            sequence: null,
            playing: null,
            videoElement: null,
            loadingSequence: false,
            isThreeSixty: false
        }
    },
    computed: {
        paused() {
            return !this.playing
        },
        xapiUri() {
            let url = this.baseXapiUri
            if (this.projectId < 1 || this.sectionId < 1) {
                url += 'media/' + this.item_id
            } else {
                url += 'projects/' + this.projectId + '/sections/' + this.sectionId + '/videos/' + this.item_id
            }
            return url
        }
    },
    mounted() {
        this.initItem()
    },
    watch: {
        'media_id': function() {
            this.initItem()
        },
        'sequence_id': function() {
            this.initItem()
        }
    },
    methods: {
        initItem() {
            this.item_id = typeof this.$route.params.id !== "undefined" ? this.$route.params.id : this.media_id
            this.projectId = this.$route.params.projectId ? this.$route.params.projectId : 0
            this.sectionId = this.$route.hash.substring('#Section-'.length)
            this.sectionId = this.sectionId.length > 0 ? this.sectionId : 0
            this.getItem()

            let data = {
                action: 'open',
                currentTime: 0
            }
            this.sendXapi(data)
        },
        isVideoElement() {
            return this.videoElement !== null && this.videoElement.currentTime > 0
        },
        updatePaused(event) {
            this.videoElement = event.target
            this.playing = !event.target.paused
            console.log('Playing: ' + this.playing)
        },
        onPlaying() {
            let data = {
                action: 'play',
                currentTime: this.videoElement.currentTime
            }
            this.sendXapi(data)
        },
        onSeek() {
            if (!this.isVideoElement()) {
                return
            }

            let data = {
                action: 'seeked',
                currentTime: this.videoElement.currentTime
            }
            this.sendXapi(data)
        },
        onPause() {
            if (this.videoElement.ended) {
                return
            }

            let data = {
                action: 'pause',
                currentTime: this.videoElement.currentTime
            }
            this.sendXapi(data)
        },
        onEnd() {
            let data = {
                action: 'ended',
                currentTime: this.videoElement.currentTime
            }
            this.sendXapi(data)
        },
        onLeave() {
            let data = {
                action: 'leave',
                currentTime: this.videoElement.currentTime
            }
            this.sendXapi(data)
        },
        async sendXapi(data) {
            data.sequenceId = this.sequence_id ? this.sequence_id : null
            data.isPlaylist = this.playListData ? true : false
            await axios.post(this.xapiUri, data)
        },
        async getItem() {
            try {
                const response = await axios.get('/rest/media/' + this.item_id)
                this.item = response.data
                // MediaType::THREE_SIXTY (1)
                this.isThreeSixty = response.data.type == 1 ? true : false
                this.videoSource = this.item.streamingURL_720p
                this.initPlayer()
            } catch (e) {
                console.error(e)
            }
        },
        insertAnnotation(thumbnail) {
            this.$emit('update:inputData', true)
            let timestampSplitted = this.annotation.attributes.timecode_formatted.split(':')
            let timestamp = '' + timestampSplitted[0] + ':' + timestampSplitted[1] + ''

            let body = this.annotation.attributes.body
            if (this.annotation.attributes.body.length > 15) {
                body = body.slice(0, 15) + '...'
            }

            let data = {
                id: this.annotation.attributes.id,
                mediaId: this.item_id,
                sequenceId: this.annotation.attributes.sequence_id || null,
                timestamp: timestamp,
                body: body,
                messageType: "Annotation",
                showThumbnail: false
            }

            data.idTag = 'Media' + data.mediaId
            data.idTag += data.sequenceId ? 'Sequence' + data.sequenceId : ''

            if (thumbnail) {
                data.showThumbnail = this.annotation.attributes.preview_thumb
            }
            this.sendXapiInsert(data)

            window.eventBus.$emit('insertedAnnotation', data)
            this.show = false
        },
        insertSequence(thumbnail) {
            this.loadingSequence = true
            axios.get('/rest/ep5/sequences/' + this.media_id + '/sequence/' + this.sequence.attributes.id).then((response) => {
                this.loadingSequence = false
                this.sequence = response.data

                let data = {
                    id: this.sequence.id,
                    mediaId: this.sequence.video_nid,
                    thumbnail: null,
                    title: this.sequence.title,
                    timestamp: this.sequence.timecode_formatted,
                    endtimeTimestamp: this.sequence.endtime_formatted
                }

                if(thumbnail){
                    data.thumbnail = this.sequence.preview_thumb
                }

                this.$emit('update:inputData', true)
                window.eventBus.$emit('insertedSequence', data)
                this.showSequenceShareDialog = false


            }, (e) => {
                this.loadingSequence = false
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Fehler beim Einfügen einer Sequenz in einen Abschnitt',
                    type: 'error'
                })
                console.error(e)
            })

        },
        insertVideo() {
            this.$emit('update:inputData', true)
            let data = {
                id: this.item.id,
                caption: this.item.caption,
                previewURL: this.item.previewURL
            }

            window.eventBus.$emit('videoUploadDone', data)
            this.showVideoShareDialog = false
        },
        reloadPlayerForPlaylist(payload){
            this.$emit('reloadPlayerForPlaylist', payload)
        },
        reloadPlayerForVideo(payload) {
            this.$emit('reloadPlayerForVideo', payload)
        },
        async sendXapiInsert(data) {
            await axios.get('/rest/xapi/annotation/' + data.id + '/insert')
        },

        getMediaQualitySettings() {
            let result = []

            if (this.item.streamingURL_720p) {
                result.push({
                    source: this.item.streamingURL_720p,
                    label: '720p',
                    weight: 0
                })
            }
            if (this.item.streamingURL_1080p) {
                result.push({
                    source: this.item.streamingURL_1080p,
                    label: '1080p',
                    weight: 1
                })
            }
            if (this.item.streamingURL_2160p) {
                result.push({
                    source: this.item.streamingURL_2160p,
                    label: '2160p',
                    weight: 2
                })
            }

            return result
        },

        async initPlayer() {

            let self = this
            this.sequence = null

            try {
                const playbackCommandsResponse = await axios.get('/rest/ep5/playbackcommands/' + this.media_id)
                if(this.sequence_id){
                    const sequenceResponse = await axios.get('/rest/ep5/sequences/' + this.media_id + '/sequence/' + this.sequence_id)
                    this.sequence = sequenceResponse.data
                }

                let cameraLookAt = null
                let cameraLocked = null
                let cameraPath = null

                if(this.sequence !== null) {
                    cameraLookAt = this.sequence.camera_look_at
                    cameraLocked = this.sequence.camera_locked
                    cameraPath = this.sequence.camera_path
                }

                let conf = {

                    current_userdata: {
                        id: this.$userId || "0",
                        name: this.$userName || "Gast",
                        picture: this.$userPicture,
                    },
                    align_top: false,
                    overlay_mode: false,
                    plugins: {
                        edubreak_sequences: {
                            sequence_default: this.sequence,
                            interface_uri: "/rest/ep5",
                            video_id: this.item.id,
                            share_callback: (player, sequence) => {
                                if(sequence && sequence.attributes.id){
                                    this.showSequenceShareDialog = true

                                    this.sequence = sequence
                                }else{
                                    this.showVideoShareDialog = true
                                }
                            },
                            changing_element_callback: (player, element) => {
                                if(element && element.type == 'sequence'){
                                    this.sequence_id = element.id
                                }
                                if(element && element.type == 'video'){
                                    this.sequence_id = null
                                }
                            }
                        },
                        edubreak_annotations: {
                            default_cid: this.annotation,
                            default_open: true,
                            interface_uri: "/rest/ep5",
                            video_id: this.item.id,
                            permission_create_comment: true,
                            current_userdata: {
                                name: "Gast"
                            },
                            visual_tags: {
                                1: '<i class="visualtag-heart">&#9829;</i>',
                                2: '<i class="visualtag-check">&#10004;</i>',
                                3: '<i class="icon-stopwatch"></i>'
                            },
                            share_enabled: true,
                            share_callback: (player, annotation) => {
                                this.show = true
                                this.annotation = annotation
                            }
                        },
                        edubreak_annotations_timebullets: {},
                        edubreak_annotations_drawings: {},
                        edubreak_playbackcommands: {
                            may_edit: true,
                            interface_uri: "/rest/ep5",
                            video_id: this.item.id,
                            playbackcommands: playbackCommandsResponse.data
                        },
                        edubreak_annotations_rating: {
                            type: "lights"
                        },
                        ...this.playListData && {edubreak_playlist: {
                            playlist: this.playListData,
                            active_position: this.playlistPosition,
                            reload_callback: function(player, playlist, position){
                                self.reloadPlayerForPlaylist({ 'playlist': playlist, 'position': position})
                            },
                            link_to_video_callback: function(player, video_nid){
                                self.reloadPlayerForVideo({ 'video_nid': video_nid})
                            }
                        }},
                        ...this.isThreeSixty && {edubreak_360: {
                            fov: 75,
                            camera_look_at: cameraLookAt,
                            camera_locked: cameraLocked,
                            camera_path: cameraPath
                        }},

                        edubreak_quality_selector: {
                            alternatives: this.getMediaQualitySettings()
                        }
                    }
                }

                //check playcommand sequence
                var sequence = playbackCommandsResponse.data.filter(obj => {
                    return obj.type === "sequence"
                })
                // Setting offset from sequence
                if (sequence[0]) {
                    conf.offset = {
                        start: sequence[0].timestamp / 1000,
                        end: (sequence[0].timestamp + sequence[0].duration) / 1000
                    }
                }
                if(window.isEp5){
                    $('.edubreakplayer').edubreakplayer(conf)
                }else{
                    this.$emit('noVideoPlayer')
                }
            } catch (e) {
                console.error(e)
            }
        }
    },
    beforeDestroy() {
        this.onLeave()
        this.$emit('onPlayerDestroy')
        window.dispatchEvent(new Event('ep5-destroy'))
    },
}
</script>

<style>
.hide-ep5{
    display: none;
}

.show-ep5{
    display: block;
}

.edubreakplayer {
    margin: 0 auto;
    height: calc(100vh - 64px);
    min-height: calc(100vh - 64px) !important;
}
</style>
