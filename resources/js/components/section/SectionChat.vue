<template>
    <div class="toolbar">
        <div>
            <div v-if="this.messageCount > 0 && !isOpen" class="sc-new-messsages-count">
                {{ this.messageCount }}
            </div>
            <v-btn tile depressed class="toolbar-button" @click="setActive">
                <v-icon v-if="!isActive">mdi-message-outline</v-icon>
                <v-icon v-else class="score color">mdi-message</v-icon>
            </v-btn>
        </div>
    </div>
</template>

<script>
import {createNamespacedHelpers} from 'vuex'

const {mapGetters, mapActions} = createNamespacedHelpers('messages')
export default {
    name: "SectionChat",
    props: {
        sectionId: {
            type: [String, Number]
        },
        sectionTitle: {
            type: String
        },
        projectId: {
            type: [String, Number]
        }
    },
    data() {
        return {
            active: false
        }
    },
    computed: {
        ...mapGetters(['getActiveChat', 'getMessageCount', 'getFirstUnreadMessageId']),
        isActive() {
            return (this.getActiveChat.sectionId === this.sectionId)
        },
        isOpen() {
            if (this.sectionId === this.getActiveChat.sectionId) {
                return this.getActiveChat.open
            }
            return false
        },
        channelUrl() {
            return 'messagecount.' + this.projectId + '-section.' + this.sectionId
        },
        messageCount() {
            return this.getMessageCount(this.sectionId)
        },
        firstUnreadMessageId() {
            return this.getFirstUnreadMessageId(this.sectionId)
        }
    },
    methods: {
        ...mapActions(['setActiveChat', 'setMessageCount']),
        setActive() {
            this.setActiveChat({
                sectionId: this.sectionId,
                messageId: this.firstUnreadMessageId,
                projectId: this.projectId,
                title: this.sectionTitle,
                open: true
            })
        },
        initEcho() {
            Echo.channel(this.channelUrl)
                .listen('.newMessageCount', (e) => {
                    if (this.getActiveChat.sectionId !== this.sectionId) {
                        this.setMessageCount({
                            sectionId: this.sectionId,
                            messageCount: this.getMessageCount(this.sectionId) + 1,
                            projectId: this.projectId,
                            firstUnreadMessageId: e.messageId
                        })
                    }
                })
        }
    },
    created() {
        this.initEcho()
    },
    beforeDestroy() {
        Echo.leave(this.channelUrl)
    }
}
</script>

<style lang="scss" scoped src="./toolbar.scss">
</style>
