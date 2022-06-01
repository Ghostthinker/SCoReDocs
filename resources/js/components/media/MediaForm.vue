<template>
    <v-row justify="center">
        <custom-dialog :dialog="inputData" v-on:close="$emit('update:inputData', false)">
            <template v-slot:title>
                Video hochladen
            </template>
            <template v-slot:content>
                <v-card-text>
                    <v-text-field v-model="caption" blabel="Regular" single-line clearable
                                  :rules="[rules.required]"
                                  placeholder="Beschriftung"></v-text-field>
                    <v-file-input
                        class="custom-file-upload"
                        :rules="[rules.size]"
                        accept="video/*"
                        show-size
                        loading="true"
                        label="Datei auswählen (max. 4 GB)" ref="uploadFile"
                        v-model="file"
                    >
                    </v-file-input>
                    <v-checkbox
                        class="custom-media-type-360"
                        v-model="mediaType360"
                        label="360° Video" ref="mediaType360"
                    ></v-checkbox>
                    <progress
                        class="file-upload-progress"
                        max="100"
                        :value.prop="uploadPercentage"
                    ></progress>
                    <v-subheader style="height: auto" class="pl-0" v-if="currentState">{{ currentState }}</v-subheader>
                </v-card-text>
                <v-alert
                    dense
                    :type="notification.type"
                    v-if="uploadError"
                >
                    {{ notification.message }}
                </v-alert>
            </template>
            <template v-slot:actions>
                <v-btn v-bind:disabled="!inputIsValid" color="primary" @click="submitForm">Speichern</v-btn>
            </template>
        </custom-dialog>
    </v-row>
</template>
<script>
import axios from 'axios'
import _ from "lodash"
import CustomDialog from "../dialog/CustomDialog"

export default {
    name: "MediaForm",
    components: {CustomDialog},
    data: () => ({
        file: null,
        mediaType360: false,
        uploadPercentage: 0,
        rules: {
            size: value => !value || value.size < 4000000000,
            required: value => !!value || 'Pflichtfeld'
        },
        caption: '',
        notification: {
            type: 'error',
            message: 'Beim Upload trat ein Fehler auf! Bitte wenden Sie sich an den Support, falls der Fehler bestehen bleibt.'
        },
        uploadError: false,
        currentState: null
    }),
    props: {
        inputData: Boolean
    },
    watch: {
        inputData: function () {
            if (this.inputData === false) {
                this.closeUploadForm()
            }
        }
    },
    computed: {
        inputIsValid() {
            if (this.file && this.caption) {
                return !(this.file.size > 4000000000 || this.caption.length < 1)
            }
            return false
        }
    },
    methods: {
        async submitForm() {
            this.uploadError = false
            this.currentState = 'Hochladen'
            if (this.file) {
                let formData = new FormData()

                formData.append("upload", this.file, this.file.name)
                // filename
                formData.append("caption", this.caption)
                // 360 flag
                formData.append("mediaType360", this.mediaType360)
                // Project Id
                let projectId = this.$route.params.projectId ? this.$route.params.projectId : 0
                formData.append("projectId", projectId)

                // disable frontend section timeout handling while upload is running
                window.eventBus.$emit('upload-started')

                try {
                    const response = await axios.post('/rest/media', formData,
                                                      {
                                                          onUploadProgress: function (progressEvent) {
                                                              const progress = Math.round((progressEvent.loaded / progressEvent.total) * 100)
                                                              this.throttleUploadRunningEvent()
                                                              if (progress >= 90) {
                                                                  this.currentState = 'Für die Konvertierung vorbereiten'
                                                              }
                                                              this.uploadPercentage = progress >= 90 ? 90 : progress
                                                          }.bind(this)
                                                      })
                    this.uploadPercentage = 100
                    this.currentState = 'Abgeschlossen'
                    this.$emit('upload-completed', response)
                } catch (e) {
                    if(e.response.status === 415) {
                        this.notification.message = "Das Dateiformat der hochgeladenen Datei wird nicht unterstützt. Es werden nur Videodateien unterstützt."
                    } else {
                        this.notification.message = e.response.data.errors.upload[0]
                    }

                    this.uploadError = true
                    this.uploadPercentage = 0
                    this.currentState = null
                }
            }
        },

        closeUploadForm() {
            this.uploadPercentage = 0
            this.file = null
            this.caption = ''
            this.mediaType360 = false
            this.uploadError = false
            this.currentState = null
        },

        throttleUploadRunningEvent: _.throttle(function () {
            this.sendUploadRunningEvent()
        }, 60000),

        async sendUploadRunningEvent() {
            window.eventBus.$emit('upload-running')
        },
    }
}
</script>

<style lang="scss">
.file-upload-progress {
  width: inherit;
}

.custom-file-upload {
  .v-input__control {
    .v-text-field__slot {
      input {
        pointer-events: none !important;
      }
    }
  }
}
</style>
