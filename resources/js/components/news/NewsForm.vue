<template>
    <v-row justify="center">
        <custom-dialog :dialog="inputData" :show-abort-button="false" :show-close-button="false"  @close="closeForm" class="dialog-news">
            <template v-slot:title>
                {{ headline }}
            </template>
            <template v-slot:content>
                <v-form
                    ref="form"
                    v-model="valid"
                    lazy-validation
                    class="form-news"
                >
                    <v-alert
                        :value="alert"
                        type="warning"
                        transition="scale-transition"
                    >
                        {{ errorMessage }}
                    </v-alert>
                    <v-card-text>
                        <v-text-field v-model="title"
                                      label="Titel"
                                      hide-details="auto"
                                      clearable
                                      :counter="190"
                                      :rules="titleRules"
                                      required
                        ></v-text-field>
                        <div></div>
                        <label class="v-label theme--light news-label">Inhalt</label>
                        <div></div>
                        <v-col :cols="12" md="12">
                            <gtckeditor class="ckeditor"
                                        ref="ckeditor"
                                        v-model="editorData"
                                        :config="editorConfig"
                                        :editor-url="editorUrl"
                                        :read-only="false"
                                        :inEdit="true"
                                        @ready="gtCkeditorReady = true; $emit('ckEditorReady')"
                            >
                            </gtckeditor>
                        </v-col>
                    </v-card-text>
                </v-form>
            </template>
            <template v-slot:actions>
                <v-btn v-if="newsToEdit" color="error" @click="showConfirmationDialog = true">Löschen</v-btn>
                <v-btn color="primary" @click="submitForm">Speichern</v-btn>
            </template>
        </custom-dialog>
        <custom-dialog narrow :dialog="showConfirmationDialog" :show-abort-button="true" @close="showConfirmationDialog = false">
            <template v-slot:title>
                Wollen Sie die News wirklich löschen?
            </template>
            <template v-slot:actions>
                <v-btn color="error" @click="deleteNews()">Löschen</v-btn>
            </template>
        </custom-dialog>
    </v-row>
</template>
<script>
import Vue from "vue"
import GTCKEditor from "../GTCKEditor"
import EditorConfigMixin from "../GTCKEditor/EditorConfigMixin"
import News from "../../models/news"
import CustomDialog from "../dialog/CustomDialog"

Vue.use(GTCKEditor)

export default {
    name: "NewsForm",
    components: {CustomDialog},
    mixins: [EditorConfigMixin],
    data() {
        return {
            title: this.newsToEdit ? this.newsToEdit.title : '',
            content: this.newsToEdit ? this.newsToEdit.content : '',
            titleRules: [
                v => !!v || 'Titel ist ein Pflichtfeld',
                v => (v && v.length <= 190) || 'Titel darf maximal 190 Zeichen haben',
            ],
            valid: false,
            alert: false,
            errorMessage: 'Angaben unvollständig',
            useEventHandler: false,
            useVideoUpload: false,
            wordcount: {
                showParagraphs: false,
                showCharCount: true,
                showWordCount: true,
                countHTML: false,
                countLineBreaks: false,
                countSpacesAsChars: true,
                maxWordCount: -1,
                maxCharCount: 4000,
                maxParagraphs: -1,
                pasteWarningDuration: 0,
            },
            showConfirmationDialog: false
        }
    },
    props: {
        inputData: Boolean,
        newsToEdit: {
            type: News
        },
        headline: {
            type: String,
            required: true
        },
        successMessage: {
            type: String,
            default: null
        },
        notAllowedMessage: {
            type: String,
            required: true
        },
        genericErrorMessage: {
            type: String,
            required: true
        }
    },
    computed: {
        editorData: {
            get: function () {
                return this.content
            },
            set: function (content) {
                this.content = content
            }
        },
    },
    methods: {
        async submitForm() {
            if (!this.valid || this.title.length === 0) {
                this.alert = true
                this.errorMessage = 'Angaben unvollständig'
                return
            }

            try {
                if (this.newsToEdit) {
                    this.newsToEdit.title = this.title
                    this.newsToEdit.content = this.content
                    const success = await this.$store.dispatch('news/putNews', this.newsToEdit)
                    if (success) {
                        this.newsToEdit.read = false
                    }
                } else {
                    await this.$store.dispatch('news/postNews', {
                        title: this.title,
                        content: this.content
                    })
                }

                if (this.successMessage) {
                    this.$store.dispatch('notifications/newNotification', {
                        message: this.successMessage,
                        type: 'success'
                    })
                }
                this.closeForm()
            } catch (e) {
                this.errorMessage = ''
                console.log(e.response)
                if (e.response.status === 403) {
                    this.errorMessage = this.notAllowedMessage
                } else if (e.response.status === 422) {
                    if(e.response.data.errors) {
                        Object.entries(e.response.data.errors).map(el => this.errorMessage += el[1] + ' ')
                    } else if (e.response.data.message) {
                        this.errorMessage = e.response.data.message
                    }
                } else {
                    this.errorMessage = this.genericErrorMessage
                }
                this.alert = true
            }
        },
        closeForm() {
            this.$emit('update:inputData', false)
            this.reset()
            this.title = ''
            this.content = ''
        },
        validate() {
            this.$refs.form.validate()
        },
        reset() {
            this.alert = false
            this.$refs.form.reset()
        },
        async deleteNews() {
            try  {
                const success = await this.$store.dispatch('news/deleteNews', this.newsToEdit)
                if (success) {
                    this.$store.dispatch('notifications/newNotification', {
                        message: "News erfolgreich gelöscht.",
                        type: 'success'
                    })
                    this.closeForm()
                } else {
                    this.$store.dispatch('notifications/newNotification', {
                        message: "Fehler beim Löschen der News.",
                        type: 'error'
                    })
                }
            } catch (e) {
                this.$store.dispatch('notifications/newNotification', {
                    message: "Fehler beim Löschen der News.",
                    type: 'error'
                })
                console.log(e)
            }
            this.showConfirmationDialog = false
        }
    },
    watch: {
        'valid': function () {
            if (this.valid === true && this.alert === true) {
                this.alert = false
            }
        }
    }
}
</script>

<style lang="scss">
    .form-news .cke {
      width: auto !important;
    }

    .news-label {
      left: 0;
      right: auto;
      position: relative;
      padding-bottom: 1rem;
    }
</style>
