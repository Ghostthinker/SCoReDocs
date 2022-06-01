<template>
    <div>
        <!-- This is just needed for the blade template -.-   -->
        <a v-if="!useIcon" class="nav-link" @click="dialog = true">Hilfe</a>
        <v-icon v-if="useIcon" @click="dialog = true" style="font-size: 30px" color="primary">mdi-help</v-icon>
        <custom-dialog :dialog="dialog" v-on:close="dialog = false">
            <template v-slot:title>
                Support Anfrage
            </template>
            <template v-slot:content>
                <p class="pt-2 pb-2">
                    <b>Haben Sie ein Problem?</b> <br> Hier können Sie mit dem SCoRe-Team Kontakt aufnehmen, egal ob es sich um
                    ein technisches Problem
                    oder eine inhaltliche Frage handelt - hier ist die richtige Anlaufstelle. Schildern Sie hierfür ihr Problem
                    und wählen die
                    passende Kategorie des Problems aus.
                </p>
                <v-form
                    ref="form"
                    v-model="valid"
                    lazy-validation
                >
                    <v-textarea
                        v-model="problemDescription"
                        name="problem-description"
                        label="Problembeschreibung"
                        placeholder="Geben Sie hier bitte eine Beschreibung des Problems an."
                        outlined
                        :rules="textareaRules"
                        :counter="maxTextSize"
                        height="250px"
                    ></v-textarea>
                    <v-select
                        v-model="selectedTopic"
                        :items="topics"
                        :rules="selectRules"
                        item-value="key"
                        item-text="text"
                        label="Kategorie (bitte auswählen)"
                        required
                        dense
                    ></v-select>
                    <v-text-field
                        v-model="email"
                        :rules="emailRules"
                        label="E-mail"
                        required
                    ></v-text-field>
                    <v-file-input
                        show-size
                        placeholder="Optionaler Dateianhang"
                        :rules="fileRules"
                        id="upload-bug-attachment"
                        name="upload-bug-attachment"
                        type="file"
                        v-model="file"
                    ></v-file-input>
                </v-form>
            </template>
            <template v-slot:actions>
                <v-spacer></v-spacer>
                <v-btn
                    color="primary"
                    :loading="loading"
                    @click="send"
                >
                    Absenden
                </v-btn>
            </template>
        </custom-dialog>
    </div>
</template>

<script>
import axios from 'axios'
import CustomDialog from "../dialog/CustomDialog"

export default {
    name: "HelpDialog",
    components: {CustomDialog},
    props: {
        user: null,
        useIcon: {
            default: false,
            type: Boolean
        }
    },
    data() {
        return {
            dialog: false,
            valid: false,
            loading: false,
            maxTextSize: 1000,
            email: this.user ? this.user.email : null,
            textareaRules: [
                v => !!v || 'Sie müssen eine Problembeschreibung angeben',
                v => (v && v.length >= 100) || 'Bitte beschreiben Sie das Problem mit einigen Worten. Mindestens 100 Zeichen sind nötig.',
                v => (v && v.length <= this.maxTextSize) || 'Die maximale Nachrichtenlänge beträgt ' + this.maxTextSize + ' Zeichen',
            ],
            selectRules: [v => !!v || 'Pflichtfeld'],
            emailRules: [
                v => !!v || 'Pflichtfeld',
                v => /.+@.+\..+/.test(v) || 'E-mail ist ungültig',
            ],
            problemDescription: '',
            selectedTopic: null,
            topics: [
                {key: 3, text: 'Inhaltlich / fachliche Frage'},
                {key: 1, text: 'Technische Frage / Problem'},
            ],
            fileRules: [
                value => !(value instanceof File) || value.size < 4000000 || 'File Größe muss kleiner als 4 MB sein!',
            ],
            file: []
        }
    },
    methods: {
        async send() {
            this.$refs.form.validate()
            if (this.valid === true) {
                this.loading = true

                let formData = new FormData()
                if (this.file) {
                    formData.append("attachment", this.file)
                }

                formData.append('email', this.email)
                formData.append('message', this.problemDescription)
                formData.append('topicId', this.selectedTopic)
                formData.append('siteUrl', window.location.href)

                await axios.post('/mail/support', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(() => {
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Ihre Nachricht wurde an das SCoRe-Team verschickt.',
                        type: 'info'
                    })
                    this.closeAndReset()
                }, (error) => {
                    console.error('Error on sending support mail:')
                    console.error(error)
                    this.$store.dispatch('notifications/persistentNotification', {
                        message: 'Beim Versenden der Nachricht trat ein Problem auf. Bitte probieren Sie es noch einmal. ' +
                            'Falls der Fehler besteht senden Sie bitte eine E-Mail direkt an support@score-docs.de.',
                        type: 'error'
                    })
                    this.loading = false
                })
            }
        },
        closeAndReset() {
            this.problemDescription = ''
            this.selectedTopic = null
            this.dialog = false
            this.loading = false
        }
    }
}
</script>

<style scoped>

</style>
