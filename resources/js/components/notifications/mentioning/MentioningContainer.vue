<template>
    <div class="mentioning-notifications-container">
        <transition name="fade" v-for="(mentioning, index) in getMessageMentionings" :key="index">
            <mentioning-alert :mentioning="mentioning" :key="mentioning.id" :index="index"></mentioning-alert>
        </transition>
    </div>
</template>

<script>
import MentioningAlert from "./MentioningAlert"
import {createNamespacedHelpers} from 'vuex'

const {mapGetters, mapActions} = createNamespacedHelpers('messages')
export default {
    name: "MentioningContainer",
    components: {
        MentioningAlert
    },
    props: {
        projectId: {
            required: true
        }
    },
    data() {
        return {

        }
    },
    computed: {
        ...mapGetters(['getMessageMentionings'])
    },
    methods: {
        ...mapActions(['fetchMessageMentionings'])
    },
    created() {
        this.fetchMessageMentionings(this.projectId)
    }
}
</script>

<style lang="scss" scoped src="../notifications.scss"></style>
