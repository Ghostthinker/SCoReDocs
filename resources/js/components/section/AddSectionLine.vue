<template>
    <div style="display: flex; flex-direction: column;">
        <div class="plus-line-container"
             @mouseover="hover = true"
             @mouseleave="hover = false"
        >
            <div v-if="hover || showPermanent" class="horizontally-centered-row">
                <v-icon class="plus-icon" @click="optionsDialog()">mdi-plus</v-icon>
                <div class="plus-line"></div>
            </div>
        </div>
        <v-row justify="center">
            <custom-dialog :dialog="showOptionsDialog" show-abort-button @close="closeOptionsDialog">
                <template v-slot:title>
                    Abschnitt Optionen
                </template>
                <template v-slot:content>
                    Mit dem Klick auf Anlegen, erstellen Sie einen neuen Abschnitt.
                    Mit dem Klick auf Wiederstellen, haben Sie die Möglichkeit bereits gelöschte Abschnitte wiederherzustellen.
                </template>
                <template v-slot:actions>
                    <v-btn color="primary" @click="addSection">Anlegen</v-btn>
                    <v-btn color="primary" @click="openSectionsList">Wiederherstellen</v-btn>
                </template>
            </custom-dialog>
        </v-row>
        <section-list v-if="showSectionsList"
                      :projectId="projectId"
                      :show="showSectionsList"
                      @close="closeSectionsList"
                      @revert-section="revertSection">
        </section-list>
    </div>
</template>

<script>
import axios from "axios"
import SectionList from './SectionList'
import CustomDialog from "../dialog/CustomDialog"
export default {
    name: "AddSectionLine",
    components: {
        CustomDialog,
        SectionList
    },
    props: {
        // eslint-disable-next-line vue/require-prop-type-constructor
        showPermanent: false,
        topIndex: Number,
        topSectionId: {
            default: "0",
            // eslint-disable-next-line vue/require-prop-type-constructor
            type: String | Number
        }
    },
    data() {
        return {
            hover: false,
            projectId: null,
            showOptionsDialog: false,
            showSectionsList: false
        }
    },
    methods: {
        optionsDialog() {
            this.showOptionsDialog = true
        },
        addSection() {
            return new Promise((resolve, reject) => {
                let url = '/rest/projects/' + this.projectId + '/sections'
                let data = {topSectionId: this.topSectionId}
                let self = this
                axios.post(url, data).then((response) => {
                    const item = response.data
                    this.showOptionsDialog = false
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Abschnitt erfolgreich angelegt',
                        type: 'success'
                    })
                    resolve(item)
                }, (e) => {
                    const response = e.response
                    let errorMessage = "Fehler beim Anlegen des Abschnitts"
                    if (response.status === 422) {
                        Object.entries(response.data.errors).map(el => errorMessage = el[1][0])
                    }
                    self.$store.dispatch('notifications/newNotification', {
                        message: errorMessage,
                        type: 'error'
                    })
                    this.showOptionsDialog = false
                    console.error(e)
                    reject(e)
                })
            })
        },
        closeOptionsDialog() {
            this.showOptionsDialog = false
        },
        openSectionsList() {
            this.showSectionsList = true
            this.showOptionsDialog = false
        },
        closeSectionsList() {
            this.showSectionsList = false
        },
        revertSection(sectionId) {
            let url = '/rest/projects/' + this.projectId + '/sections/' + sectionId + '/revert/' + this.topSectionId
            axios.get(url).then(() => {
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Abschnitt wurde erfolgreich wiederhergestellt',
                    type: 'success'
                })
            }, (e) => {
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Fehler beim Wiederherstellen eines Abschnittes',
                    type: 'error'
                })
                console.error(e)
                reject(e)
            })
        }
    },
    mounted() {
        this.projectId = this.$route.params.projectId
    }
}
</script>

<style scoped type="scss">
.plus-line-container {
    height: 2em;
}

.editor {
    background-color: grey;
    width: 100%;
    height: 200px;
}

.plus-icon {
    border: 2px solid var(--v-primary-base);
    border-radius: 50%;
    margin-right: 0.3em;
    color: var(--v-primary-base) !important;
}

.plus-line {
    height: 1px;
    border: 2px dashed var(--v-primary-base);
    flex-grow: 1;
}
</style>
