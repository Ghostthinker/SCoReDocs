<template>
    <v-form
        ref="form"
        v-model="valid"
        lazy-validation
    >
        <v-row justify="center">
            <custom-dialog :dialog="inputData" show-abort-button @close="closeForm">
                <template v-slot:title>
                    Abschnitt speichern
                </template>
                <template v-slot:content>
                    <v-alert
                        :value="alert"
                        type="warning"
                        transition="scale-transition"
                    >
                        Angaben unvollständig
                    </v-alert>
                    <v-alert
                        :value="alertStatusPhaseOne"
                        type="warning"
                        transition="scale-transition"
                    >
                        Durch ändern des Status in "Eingereicht" wird der Prüfungsvorgang durch einen
                        Lernbegleiter angestoßen
                    </v-alert>
                    <v-alert v-for="(message, index) in errorMsg" :key="index"
                             :value="(message.text.length > 0)"
                             type="error"
                             transition="scale-transition"
                    >
                        {{ message.text }}
                    </v-alert>
                    <v-card-text>
                        <v-text-field v-model="changeLog"
                                      label="Änderungsbeschreibung"
                                      hide-details="auto"
                                      clearable
                                      :counter="191"
                                      :rules="changeLogRules"
                                      required
                        ></v-text-field>
                        <v-checkbox
                            v-model="isMinorUpdate"
                            :label="'Kleine Korrektur (Änderung wird nicht in den Aktivitäten angezeigt)'"
                        ></v-checkbox>
                    </v-card-text>
                </template>
                <template v-slot:actions>
                    <v-btn color="primary" @click="onSaveSection">Speichern</v-btn>
                </template>
            </custom-dialog>
        </v-row>
    </v-form>
</template>

<script>
import axios from "axios"
import {createNamespacedHelpers} from "vuex"
import CustomDialog from "../dialog/CustomDialog"

const {mapGetters} = createNamespacedHelpers('projects')

export default {
    name: "SectionForm",
    components: {CustomDialog},
    data() {
        return {
            changeLog: '',
            changeLogRules: [
                v => !!v || 'Änderungsbeschreibung ist ein Pflichtfeld',
                v => (v && v.length <= 191) || 'Änderungsbeschreibung darf maximal 191 Zeichen haben',
            ],
            valid: false,
            alert: false,
            errorMsg: [],
            isMinorUpdate: false
        }
    },
    props: {
        inputData: Boolean,
        section: Object,
        editorData: String,
        url: String,
        additionalChangeLog: String
    },
    computed: {
        ...mapGetters(['getProject']),
        alertStatusPhaseOne() {
            return ((this.section.status === 2) && (this.section.heading === 1) &&
                (this.section.oldStatus !== 2) && this.getProject.type !== "AssessmentDoc")
        }
    },
    methods: {
        async onSaveSection() {

            this.errorMsg = []

            if (!this.valid || this.changeLog.length === 0) {
                this.alert = true
                return
            }

            let log = this.changeLog
            if (this.additionalChangeLog) {
                log = this.additionalChangeLog + ' Beschreibung: ' + this.changeLog
            }

            try {
                await this.saveSection({
                    content: this.editorData,
                    locked: false,
                    changeLog: log,
                    locked_at: null,
                    locking_user: null,
                    heading: this.section.heading,
                    title: this.section.title,
                    status: this.section.status,
                    isMinorUpdate: this.isMinorUpdate ?? false
                })
                this.$emit('saved')
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Speicherung erfolgreich',
                    type: 'success'
                })
                this.closeForm()
            } catch (e) {
                const response = e.response
                if (response.status === 422) {
                    Object.entries(response.data.errors).map(el => this.errorMsg.push({'text': el[1][0]}))
                } else if (response.status === 427) {
                    this.errorMsg.push({'text': response.data.errors})
                } else {
                    this.errorMsg.push({'text': response.data.message})
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Speichern war nicht erfolgreich',
                        type: 'error'
                    })
                }
            }

        },
        saveSection(data) {
            return axios.put(this.url, data)
        },
        closeForm() {
            this.$emit('update:inputData', false)
            this.reset()
            this.changeLog = ''
            this.errorMsg = []
        },
        validate() {
            this.$refs.form.validate()
        },
        reset() {
            this.alert = false
            this.$refs.form.reset()
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

<style scoped>
>>> .v-label {
  margin: 0;
}
</style>
