<template>
    <v-alert
        :type="notification.type"
        prominent
        class=""
        :color="alertColor(notification)"
        :isDownload="notification.isDownload"
    >
        <v-row align="center">
            <v-col class="grow">
                {{ notification.message }}
            </v-col>
            <v-col class="shrink icon-col">
                <v-icon class="icon" @click="$emit('close', index)">mdi-close</v-icon>
            </v-col>
        </v-row>
        <NotificationDownload v-if="notification.isDownload" :data="notification" @close="$emit('close', index)"></NotificationDownload>
        <NotificationDownloadPlaylist v-if="notification.isPlaylistDownload" :data="notification" @close="$emit('close', index)"></NotificationDownloadPlaylist>
    </v-alert>
</template>

<script>
import NotificationDownload from "./NotificationDownload";
import NotificationDownloadPlaylist from "./NotificationDownloadPlaylist";
export default {
    name: "NotificationAlert",
    components: {NotificationDownloadPlaylist, NotificationDownload},
    props:{
        notification: {
            type: Object
        },
        index: {
            type: Number
        },
        isDownload: {
            type: Boolean
        }
    },
    methods: {
        alertColor(notification){
            if(notification.type === "success"){
                return '#00a58d'
            }
            return null
        }
    },
    created() {

    }

}
</script>

<style scoped>
    .v-alert {
        color: #fff;
        margin-right: 1rem;
        width: calc(100% - 1rem);
    }
    .icon-col{
        min-width: unset;
    }
    .icon{
        cursor: pointer;
    }
</style>
