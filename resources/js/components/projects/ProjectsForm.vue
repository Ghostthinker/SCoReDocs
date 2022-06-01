<template>
    <v-form
        ref="form"
        v-model="valid"
        lazy-validation
    >
        <v-row justify="center">
            <custom-dialog :dialog="inputData" @close="closeForm">
                <template v-slot:title>
                    {{ headline }}
                </template>
                <template v-slot:content>
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
                                      :counter="191"
                                      :rules="titleRules"
                                      required
                        ></v-text-field>
                        <v-textarea v-model="description"
                                    label="Beschreibung"
                                    hide-details="auto"
                        ></v-textarea>
                        <v-checkbox
                            v-model="basicCourse"
                            :label="'Basiskurs'"
                        ></v-checkbox>
                    </v-card-text>
                </template>
                <template v-slot:actions>
                    <v-btn color="primary" @click="submitForm">Speichern</v-btn>
                </template>
            </custom-dialog>
        </v-row>
    </v-form>
</template>
<script>
import Project from "../../models/project"
import CustomDialog from "../dialog/CustomDialog"

export default {
    name: "ProjectsForm",
    components: {CustomDialog},
    data() {
        return {
            title: '',
            description: '',
            titleRules: [
                v => !!v || 'Titel ist ein Pflichtfeld',
                v => (v && v.length <= 191) || 'Titel darf maximal 191 Zeichen haben',
            ],
            valid: false,
            alert: false,
            errorMessage: 'Angaben unvollständig',
            basicCourse: false
        }
    },
    props: {
        inputData: Boolean,
        requestURI: {
            type: String,
            required: true
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
    methods: {
        async submitForm() {

            if (!this.valid || this.title.length === 0) {
                this.alert = true
                this.errorMessage = 'Angaben unvollständig'
                return
            }

            try {
                const response = await this.$store.dispatch('projects/postProject', {
                    title: this.title,
                    description: this.description,
                    basic_course: this.basicCourse || false
                })

                const project = Project.create(response.data)
                this.$emit('addProject', project)
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
                    if (e.response.data.errors) {
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
            this.description = ''
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
  margin: 0px;
}
</style>
