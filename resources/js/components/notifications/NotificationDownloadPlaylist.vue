<template>
    <v-col class="download-progress-container">
        <v-row class="playlist-progress-row">{{this.step}}</v-row>
        <v-progress-linear
            v-if="status !== 'pending' && status !== 'concat' && status !== 'concatenated' "
            height="1.25em"
            rounded
            class="download-progress-bar"
            color="cyan"
            v-model="progress"
        >
            <template v-slot:default="{ value }">
                <strong>{{ Math.ceil(value) }}%</strong>
            </template>
        </v-progress-linear>
        <v-progress-linear
            v-if="status === 'pending' || status === 'concat'"
            height="1.25em"
            rounded
            color="cyan"
            indeterminate
        >
        </v-progress-linear>
        <v-row v-if="status === 'lossless' || status === 'clipping'" class="playlist-progress-row">Videos: {{this.convertLosslessDone}}/{{this.convertLosslessOpen}}</v-row>
        <v-row v-if="status === 'lossless' || status === 'clipping'" class="playlist-progress-row">Sequenzen: {{this.clippingDone}}/{{this.clippingOpen}}</v-row>
        <v-row v-if="status === 'padding'" class="playlist-progress-row">Formate angeglichen: {{this.convertPaddingDone}}/{{this.playlistSize}}</v-row>
        <v-row v-if="status === 'concat'" class="playlist-progress-row">Zusammengeführt: {{this.concatDone}}/{{this.playlistSize}}</v-row>
        <v-row class="download-button-container">
            <v-btn small :disabled="isDisabled" @click="downloadPlaylist">Download</v-btn>
        </v-row>
    </v-col>
</template>

<script>
export default {
    name: "NotificationDownloadPlaylist",
    data() {
        return {
            progress: 0,
            progressUpdate: null,
            isDisabled: true,
            progressType: 'normal',
            status: 'pending',
            step: 'Playlist wurde für Aufbereitung eingereiht. Sobald mit der Aufbereitung begonnen wurde wird der Status hier angezeigt.',
            clippingDone: 0,
            clippingOpen: 0,
            convertLosslessDone: 0,
            convertLosslessOpen: 0,
            convertPaddingDone: 0,
            concatDone: 0,
            playlistSize: 0,
            downloadUrl: ''
        }
    },
    props: {
        data: {
            type: Object
        }
    },
    mounted() {
        this.progressUpdate = window.setInterval(() => {
            this.updateProgress()
        }, 5000)
    },
    beforeDestroy() {
        window.clearInterval(this.progressUpdate)
    },
    methods: {
        updateProgress() {
            axios.get('/rest/section/' + this.data.sectionId + '/playlist/download/progress/' + this.data.playlistId).then((response) => {
                this.playlistSize = parseFloat(response.data.playlistSize)
                this.downloadUrl = response.data.downloadUrl
                this.status = response.data.status

                this.clippingOpen = parseFloat(response.data.clippingOpen)
                this.convertLosslessOpen = parseFloat(response.data.convertLosslessOpen)

                this.convertLosslessDone = parseFloat(response.data.convertLosslessDone)
                this.clippingDone = parseFloat(response.data.clippingDone)
                this.convertPaddingDone = parseFloat(response.data.convertPaddingDone)
                this.concatDone = parseFloat(response.data.concatDone)

                if(response.data.progress !== null) {
                    this.progress = parseFloat(response.data.progress)
                }

                switch (this.status) {
                case 'lossless':
                    this.step = 'Videos & Sequenzen aufbereiten'
                    break
                case 'clipping':
                    this.step = 'Videos & Sequenzen aufbereiten'
                    break
                case 'padding':
                    this.step = 'Formate der aufbereiteten Videos & Sequenzen vereinheitlichen.'
                    break
                case 'concat':
                    this.step = 'Videos & Sequenzen zusammenführen.'
                    break
                case 'pending':
                    this.step = 'Playlist wurde für Aufbereitung eingereiht. Sobald mit der Aufbereitung begonnen wurde, wird der Status hier angezeigt.'
                    break
                case 'concatenated':
                    this.step = 'Aufbereitung der Playlist abgeschlossen, bereit zum herunterladen.'
                    break
                case 'error':
                    this.step = 'Fehler bei der Aufbereitung der Playlist. Bitte starten sie den Download neu.'
                    break
                default:
                    this.step = 'Status konnte nicht ausgewertet werden.'
                }

                if(this.status === 'concatenated') {
                    this.handlePreparationFinished()
                }
            })
        },
        handlePreparationFinished() {
            window.clearInterval(this.progressUpdate)
            this.isDisabled = false
        },
        downloadPlaylist() {
            window.open(this.downloadUrl)
            this.$emit('close')
        }
    }
}
</script>

<style scoped>
    .download-progress-container {
        padding: 1em;
    }
    .download-button-container {
        margin-top: 1em;
        padding-right: 1em;
        justify-content: flex-end;
    }
    .playlist-progress-row {
        margin-left: 0;
    }
</style>
