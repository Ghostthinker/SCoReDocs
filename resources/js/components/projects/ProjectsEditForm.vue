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
                        <v-select
                            label="Typ des Projekts"
                            item-text="translated"
                            item-value="value"
                            :items="filteredProjectTypes"
                            v-model="projectType"
                        >
                        </v-select>
                        <v-checkbox
                            v-model="basicCourse"
                            :label="'Basiskurs'"
                        ></v-checkbox>
                        <br/>
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
import {createNamespacedHelpers} from 'vuex'
import CustomDialog from "../dialog/CustomDialog"

const {mapGetters, mapActions} = createNamespacedHelpers('projects')
export default {
    name: "ProjectsEditForm",
    components: {CustomDialog},
    data() {
        return {
            valid: false,
            alert: false,
            errorMessage: 'Angaben unvollständig',
            projectType: this.project.type,
            title: this.project.title,
            description: this.project.description,
            titleRules: [
                v => !!v || 'Titel ist ein Pflichtfeld',
                v => (v && v.length <= 191) || 'Titel darf maximal 191 Zeichen haben',
            ],
            basicCourse: this.project.basicCourse
        }
    },
    props: {
        inputData: Boolean,
        project: {
            default: null,
            type: Object
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
        ...mapGetters(['getProjectTypes']),
        filteredProjectTypes() {
            // eslint-disable-next-line no-unused-vars
            const {ASSESSMENT_DOC, TEMPLATE, ...partialTypes} = this.getProjectTypes
            return Object.values(partialTypes)
        }
    },
    methods: {
        ...mapActions(['putProject']),
        submitForm() {
            if (!this.valid || this.title.length === 0) {
                this.alert = true
                this.errorMessage = 'Angaben unvollständig'
                return
            }

            this.putProject({
                ...this.project, ...{
                    type: this.projectType,
                    title: this.title,
                    description: this.description,
                    basic_course: this.basicCourse || false
                }
            }).then(() => {
                if (this.successMessage) {
                    this.$store.dispatch('notifications/newNotification', {
                        message: this.successMessage,
                        type: 'success'
                    })
                }
                this.closeForm()
            }).catch(e => {
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
            })
        },
        closeForm() {
            this.$emit('update:inputData', false)
            this.reset()
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
