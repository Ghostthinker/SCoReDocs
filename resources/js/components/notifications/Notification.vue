<template>
    <div class="notification-container">
        <transition name="fade" v-for="(notification, index) in getNotifications"
                    :key="index">
            <notification-alert :notification="notification" :index="index" @close="close"></notification-alert>
        </transition>
    </div>
</template>

<script>
import { createNamespacedHelpers } from 'vuex'
const { mapGetters, mapActions } = createNamespacedHelpers('notifications')
import NotificationAlert from "./NotificationAlert"

export default {
    name: "notification",
    components: {
        NotificationAlert
    },
    computed: {
        ...mapGetters(['getNotifications'])
    },
    methods: {
        ...mapActions(['newNotification', 'closeNotification']),
        close(index){
            this.closeNotification(index)
        }
    }
}
</script>

<style lang="scss" scoped src="./notifications.scss"></style>
