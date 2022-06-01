<template>
    <v-row class="download-progress-container">
        <v-progress-linear
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
        <v-row class="download-button-container justify-end">
            <v-btn small :disabled="isDisabled" @click="downloadSequence">Download</v-btn>
        </v-row>
    </v-row>
</template>

<script>
import axios from "axios"

export default {
    name: "NotificationDownload",
    data() {
        return {
            progress: 0,
            progressUpdate: null,
            isDisabled: true
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
        }, 2000)
    },
    beforeDestroy() {
        window.clearInterval(this.progressUpdate)
    },
    methods: {
        updateProgress() {
            axios.get('/rest/ep5/sequences/' + this.data.videoId + '/sequence/' + this.data.sequenceId + '/download/progress').then((response) => {
                this.progress = response.data.percentage
                if(response.data.percentage >= 100) {
                    this.handleConversionFinished()
                }
            })
        },
        handleConversionFinished() {
            window.clearInterval(this.progressUpdate)
            this.isDisabled = false
        },
        downloadSequence() {
            window.open(this.data.downloadUrl)
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
    }
</style>
