<template>
    <beautiful-chat :participants="participants"
                    :projectId="projectId"
                    :titleImageUrl="titleImageUrl"
                    :onMessageWasSent="onMessageWasSent"
                    :messageList="messageList"
                    :newMessagesCount="newMessagesCount"
                    :isOpen="isChatOpen"
                    :close="closeChat"
                    :changeContext="changeContext"
                    :changeContextTooltip="changeContextTooltip"
                    :icons="icons"
                    :open="openChat"
                    :showEmoji="true"
                    :showFile="false"
                    :showEdition="false"
                    :showDeletion="false"
                    :showReply="true"
                    :showTypingIndicator="showTypingIndicator"
                    :showLauncher="true"
                    :showChangeContextButton="showChangeContextButton"
                    :colors="colors"
                    :alwaysScrollToBottom="alwaysScrollToBottom"
                    :messageStyling="messageStyling"
                    :typingUserArray="typingUserArray"
                    :title="chatTitle"
                    :jumpToMessage="messageId"
                    @onType="handleOnType"
                    @edit="editMessage"/>
</template>

<script>
import Chat from '../../../vue-beautiful-chat'
import OpenIcon from '../../../vue-beautiful-chat/src/assets/logo-no-bg.svg'
import CloseIcon from '../../../vue-beautiful-chat/src/assets/close-icon.png'
import FileIcon from '../../../vue-beautiful-chat/src/assets/file.svg'
import CloseIconSvg from '../../../vue-beautiful-chat/src/assets/close.svg'
import axios from 'axios'
import Vue from 'vue'

import {createNamespacedHelpers} from 'vuex'

// eslint-disable-next-line no-undef
Vue.use(Chat)
const {mapGetters, mapActions} = createNamespacedHelpers('messages')

export default {
    name: 'Chat',
    components: {
        // eslint-disable-next-line vue/no-unused-components
        Chat
    },
    props: {
        projectId: {
            type: [String, Number]
        }
    },
    data() {
        return {
            icons: {
                open: {
                    img: OpenIcon,
                    name: 'default'
                },
                close: {
                    img: CloseIcon,
                    name: 'default'
                },
                file: {
                    img: FileIcon,
                    name: 'default'
                },
                closeSvg: {
                    img: CloseIconSvg,
                    name: 'default'
                }
            },
            participants: [], // the list of all the participant of the conversation. `name` is the user name, `id` is used to establish the author of a message, `imageUrl` is supposed to be the user avatar.
            titleImageUrl: '/assets/images/default_user.png',
            messageList: [], // the list of the messages to show, can be paginated and adjusted dynamically
            isChatOpen: false, // to determine whether the chat window should be open or closed
            showTypingIndicator: '', // when set to a value matching the participant.id it shows the typing indicator for the specific user
            typingUserArray: [],
            colors: {
                header: {
                    bg: '#00a58d',
                    text: '#ffffff'
                },
                launcher: {
                    bg: '#00a58d'
                },
                messageList: {
                    bg: '#ffffff'
                },
                sentMessage: {
                    bg: '#00a58d',
                    text: '#ffffff'
                },
                receivedMessage: {
                    bg: '#eaeaea',
                    text: '#222222'
                },
                userInput: {
                    bg: '#f4f7f9',
                    text: '#565867'
                }
            }, // specifies the color scheme for the component
            alwaysScrollToBottom: true, // when set to true always scrolls the chat to the bottom when new events are in (new message, user starts typing...)
            messageStyling: true, // enables *bold* /emph/ _underline_ and such (more info at github.com/mattezza/msgdown)
            timeouts: {},
            lastContextChat: null,
            projectChat: {
                sectionId: null,
                projectId: this.projectId,
                open: false,
                title: 'Projekt Chat'
            },
            showChangeContextButton: false,
            messageId: null
        }
    },
    computed: {
        ...mapGetters(['getActiveChat', 'getMessageCountByPid', 'getMessageCountList']),
        channelUrl() {
            return this.buildChannelUrl(this.getActiveChat)
        },
        newMessagesCount() {
            return this.getMessageCountByPid(this.projectId)
        },
        chatTitle() {
            return this.getActiveChat.title
        },
        changeContextTooltip() {
            if (this.lastContextChat && this.getActiveChat.sectionId === null) {
                return 'Zum Chat mit Kontext: "' + this.lastContextChat.title + '"'
            } else if (this.getActiveChat.sectionId !== null) {
                return 'Zum Projekt Chat'
            }
            return ''
        },
        getMessagesUrl() {
            if (this.getActiveChat.sectionId !== null) {
                return '/rest/messages/get/' + this.projectId + '/' + this.getActiveChat.sectionId
            } else {
                return '/rest/messages/get/' + this.projectId
            }
        },
        getUser() {
            return this.$store.getters['user/getUser']
        }
    },
    methods: {
        ...mapActions(['setActiveChat', 'setMessageCount']),
        onMessageWasSent(message) {
            this.messageId = null
            // called when the user sends a message
            axios.post('/rest/message/send/' + this.projectId + (this.getActiveChat.sectionId ? '/' + this.getActiveChat.sectionId : ''), message)
                .then((response) => {
                    response.data.author = "me"
                    this.messageList = [...this.messageList, response.data]
                    this.sendXapi('send', {message_id: response.data.id})
                }, (e) => {
                    console.error(e)
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Nachricht konnte nicht gesendet werden',
                        type: 'error'
                    })
                })
        },
        openChat() {
            // called when the user clicks on the fab button to open the chat
            this.setParticipants()

            // Setting firstUnreadMessageId in active chat
            if (!this.getActiveChat.sectionId) {
                this.getActiveChat.messageId = this.getMessageCountList.filter(val => !val.sectionId)[0].firstUnreadMessageId
            } else {
                this.getActiveChat.messageId = this.getMessageCountList.filter(val => val.sectionId == this.getActiveChat.sectionId)[0].firstUnreadMessageId
            }

            this.messageId = this.getActiveChat.messageId
            this.isChatOpen = true
            this.messageCount = 0
            this.getActiveChat.open = true
            this.getActiveChat.messageId = null
            this.setMessageCount({
                sectionId: this.getActiveChat.sectionId,
                projectId: this.projectId,
                messageCount: 0,
                firstUnreadMessageId: null
            })
            axios.get('/rest/messages/updateReadMessages/' + this.projectId + '/' + this.getActiveChat.sectionId)
        },
        closeChat() {
            // called when the user clicks on the botton to close the chat
            this.isChatOpen = false
            this.setMessageCount({
                sectionId: this.getActiveChat.sectionId,
                projectId: this.projectId,
                messageCount: 0,
                firstUnreadMessageId: null
            })
            this.getActiveChat.open = false
            this.sendXapi('close')
        },
        handleScrollToTop() {
            // called when the user scrolls message list to top
            // leverage pagination for loading another page of messages
        },
        handleOnType(content) {
            // eslint-disable-next-line no-undef
            let channel = Echo.private(this.channelUrl)
            setTimeout(() => {
                channel.whisper('typing', {
                    author: this.getUser,
                    typing: true
                })
            }, 300)
            if (content.length === 1) {
                this.sendXapi('typing')
            }
        },
        editMessage(message) {
            console.log('Not implemented yet: ', message)
        },
        setOnlineStatusOfParticipants(user, online = true) {
            this.participants.forEach((item) => {
                if (user.id === item.id) {
                    item.online = online
                }
            })
            return this.participants
        },
        setParticipants() {
            return axios.get('/rest/messages/participants/project/' + this.projectId).then((response) => {
                this.participants = []
                response.data.forEach((item) => {
                    this.participants = [...this.participants, {
                        id: item.id,
                        name: item.name,
                        imageUrl: item.avatar || '/assets/images/default_user.png',
                        online: false,
                        deleted: item.deleted,
                        showUserInParticipantList: item.showUserInParticipantList
                    }]
                })
                // Renaming users if their name is not unique
                this.participants.forEach(par => {
                    const filteredParticipants = this.participants.filter(fil => fil.name === par.name)
                    if (filteredParticipants.length > 1) {
                        filteredParticipants.map((hit, index) => hit.name = hit.name + '_' + (index + 1))
                    }
                })
                return this.participants
            }, (e) => {
                console.error(e)
                return []
            })
        },
        setMessages() {
            return new Promise((resolve, reject) => {
                axios.get(this.getMessagesUrl).then((response) => {
                    response.data.forEach((item) => {
                        this.messageList = [...this.messageList, item]
                    })
                    resolve(this.messageList)
                }, e => {
                    console.error(e)
                    reject(e)
                })
            })
        },
        getMessageCounts() {
            return new Promise((resolve, reject) => {
                axios.get('/rest/messages/unreadMessageCounts/' + this.projectId).then((response) => {
                    response.data.forEach((item) => {
                        this.setMessageCount({
                            sectionId: item.sectionId,
                            projectId: item.projectId,
                            messageCount: item.unreadMessageCount,
                            userWasInvolvedInSection: item.userWasInvolvedInSection,
                            firstUnreadMessageId: item.firstUnreadMessageId
                        })
                    })
                    if (!this.getActiveChat.sectionId && !this.getActiveChat.projectId) {
                        const firstUnreadMessageId = this.getMessageCountList.filter(val => !val.sectionId)[0].firstUnreadMessageId
                        this.setActiveChat({
                            ...this.getActiveChat,
                            projectId : null,
                            messageId : firstUnreadMessageId
                        })
                    }
                }, e => {
                    console.error(e)
                    reject(e)
                })
            })
        },
        changeContext() {
            let oldSectionId = null
            if (this.getActiveChat.sectionId !== null) {
                oldSectionId = this.getActiveChat.sectionId
                this.lastContextChat = this.getActiveChat
                this.setActiveChat(this.projectChat)
            } else {
                this.setActiveChat(this.lastContextChat)
            }
            this.messageId = this.getActiveChat.messageId
            this.sendXapi('switch', {'oldSectionId': oldSectionId})
        },
        initEcho() {
            // eslint-disable-next-line no-undef
            Echo.join(this.channelUrl)
                .here((users) => {
                    for (const user of users) {
                        this.setOnlineStatusOfParticipants(user)
                    }
                })
                .joining((user) => {
                    this.setOnlineStatusOfParticipants(user)
                })
                .leaving((user) => {
                    this.setOnlineStatusOfParticipants(user, false)
                }).listen('.newMessage', (e) => {
                    this.setMessageCount({
                        sectionId: null,
                        projectId: this.projectId,
                        messageCount: this.getMessageCountByPid(this.projectId, true) + 1,
                        firstUnreadMessageId: !e.sectionId ? e.message.id : null
                    })
                    this.messageList = [...this.messageList, e.message]
                    this.messageId = null
                })
            Echo.private(this.channelUrl).listenForWhisper('typing', (e) => {
                this.messageId = null
                const userId = e.author.id
                if (this.timeouts[userId]) {
                    clearTimeout(this.timeouts[userId])
                    delete this.timeouts[userId]
                }
                this.typingUserArray.push(userId)
                this.typingUserArray = this.typingUserArray.filter((item, pos, self) => {
                    return self.indexOf(item) === pos
                })

                this.timeouts[userId] = setTimeout(() => {
                    this.typingUserArray = this.typingUserArray.filter((value) => {
                        return value !== userId
                    })
                }, 1000)
            })
        },
        buildChannelUrl(activeChat) {
            let section = activeChat.sectionId ? '-section.' + activeChat.sectionId : ''
            return 'message.' + this.projectId + section
        },
        async sendXapi(action, additionalData = {}) {
            let data = {
                action: action,
                section: this.getActiveChat.sectionId
            }
            data = {...data, ...additionalData}
            await axios.post('/rest/xapi/projects/' + this.projectId + '/chat', data)
        }
    },
    mounted() {
        this.setParticipants().then(() => {
            this.initEcho()
        })
        this.setMessages().then(() => {
            //console.log('Successfully loaded messages')
        })
        this.getMessageCounts()
    },
    watch: {
        getActiveChat: function (newValue, oldValue) {
            this.showChangeContextButton = true
            if (this.getActiveChat.open) {
                this.sendXapi('open')
            }
            if (this.getActiveChat.sectionId || this.getActiveChat.projectId) {
                this.openChat()
            }
            Echo.leave(this.buildChannelUrl(oldValue))
            this.initEcho()
            this.messageList = []
            this.setMessages().then(() => {

            })
        }
    }
}
</script>

<style>
    .sc-chat-window {
        position: unset!important;
        margin: -7em 1em 8em 0;
        min-height: 590px;
        width: 360px!important;
    }
</style>
