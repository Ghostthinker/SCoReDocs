<template>
    <v-form
        ref="form"
        v-model="valid"
        lazy-validation
    >
        <v-row justify="center">
            <custom-dialog :dialog="showDialog" show-abort-button @close="closeForm">
                <template v-slot:title>
                    Abschnitt löschen
                </template>
                <template v-slot:content>
                    <v-alert
                        :value="alert"
                        type="warning"
                        transition="scale-transition"
                    >
                        Angaben unvollständig
                    </v-alert>
                    <v-alert v-for="(message, index) in errorMsg" :key="index"
                             :value="(message.text.length > 0)"
                             type="error"
                             transition="scale-transition"
                    >
                        {{ message.text }}
                    </v-alert>
                    <v-card-text>
                        <h3>Möchten Sie wirklich löschen?</h3>
                        <v-text-field v-model="changeLog"
                                      label="Bitte Begründung für das Löschen angeben"
                                      hide-details="auto"
                                      clearable
                                      :counter="191"
                                      :rules="changeLogRules"
                                      required
                        ></v-text-field>
                    </v-card-text>
                </template>
                <template v-slot:actions>
                    <v-btn color="red" dark @click="onDeleteSection">Löschen</v-btn>
                </template>
            </custom-dialog>
        </v-row>
    </v-form>
</template>

<script>
import axios from "axios"
import CustomDialog from "../dialog/CustomDialog"

export default {
    name: "SectionDeleteLog",
    components: {CustomDialog},
    data() {
        return {
            changeLog: '',
            changeLogRules: [
                v => !!v || 'Begründung ist ein Pflichtfeld',
                v => (v && v.length <= 191) || 'Begründung darf maximal 191 Zeichen haben',
            ],
            valid: false,
            alert: false,
            errorMsg: []
        }
    },
    props: {
        showDialog: Boolean,
        section: Object,
        url: String,
    },
    methods: {
        async onDeleteSection() {

            this.errorMsg = []

            if (!this.valid || this.changeLog.length === 0) {
                this.alert = true
                return
            }

            try {
                await this.deleteSection({
                    changeLog: this.changeLog
                })
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Löschen erfolgreich',
                    type: 'success'
                })
                // can throw error (by reset)
                this.closeForm()
            } catch (e) {
                const response = e.response
                if (!response) {
                    // internal maybe reset form
                    console.error('Internal: ' + this.$refs.form)
                    console.error(e)
                    return
                }

                if (response.status === 422) {
                    Object.entries(response.data.errors).map(el => this.errorMsg.push({'text': el[1][0]}))
                    return
                }
                if (response.status === 427) {
                    this.errorMsg.push({'text': response.data.errors})
                    return
                }

                this.errorMsg.push({'text': response.data.message})
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Löschen war nicht erfolgreich',
                    type: 'error'
                })
            }

        },
        deleteSection(data) {
            return axios.delete(this.url, {data: data})
        },
        closeForm() {
            this.reset()
            this.$emit('update:showDialog', false)
            this.changeLog = ''
            this.errorMsg = []
        },
        validate() {
            this.$refs.form.validate()
        },
        reset() {
            this.alert = false
            //this.$refs.form.reset()
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

</style>
