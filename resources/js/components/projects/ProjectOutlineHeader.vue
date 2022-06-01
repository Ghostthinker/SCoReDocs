<template>
    <div class="menu-title-container">
        Inhaltsverzeichnis
        <v-btn icon class="mr-3" color="primary" @click="showModal = true">
            <v-icon large>mdi-checkbox-multiple-marked-circle</v-icon>
        </v-btn>
        <custom-dialog :dialog="showModal" :showCloseButton="false" @close="showModal = false">
            <template v-slot:title>
                Aktivitäten und Nachrichten als gelesen markieren
            </template>
            <template v-slot:content>
                Sind Sie sicher, dass Sie alle Notifications auf “gelesen” setzen möchten?
            </template>
            <template v-slot:actions>
                <v-btn color="primary" text @click="showModal = false">Nein</v-btn>
                <v-btn color="primary" @click="markAllActivityAndMessagesAsRead">Ja</v-btn>
            </template>
        </custom-dialog>
    </div>
</template>

<script>

import {createNamespacedHelpers} from "vuex"
import CustomDialog from "../dialog/CustomDialog"
const {mapActions} = createNamespacedHelpers('activities')

export default {
    name: "ProjectOutlineHeader",
    components: {CustomDialog},
    data() {
        return {
            showModal: false
        }
    },
    methods: {
        ...mapActions(['markAllActivitiesAsRead']),
        markAllActivityAndMessagesAsRead() {
            this.markAllActivitiesAsRead(this.$route.params.projectId)
            this.$store.dispatch('messages/markAllMessagesAsRead', this.$route.params.projectId)
            this.$store.dispatch('messages/markAllAsReadXapi', this.$route.params.projectId)
            this.showModal = false
        }
    }
}
</script>

<style scoped>
.menu-title-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>
