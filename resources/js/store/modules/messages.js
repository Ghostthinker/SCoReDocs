import axios from "axios"

const state = {
    activeChat: {
        sectionId: null,
        projectId: null,
        open: false,
        title: 'Projekt Chat'
    },
    messageCountList: [],
    messageMentionings: []
}

const getters = {
    getActiveChat: (state) => state.activeChat,
    getMessageCountList: (state) => state.messageCountList,
    getMessageCount: (state) => (sectionId) => {
        let found = state.messageCountList.find((el) => {
            return (sectionId && el.sectionId == sectionId)
        })
        if (!found) {
            return 0
        } else {
            return found.messageCount
        }
    },
    getUserWasInvolvedInSection: (state) => (sectionId) => {
        let found = state.messageCountList.find((el) => {
            return (sectionId && el.sectionId == sectionId)
        })
        if (!found) {
            return false
        } else {
            return found.userWasInvolvedInSection
        }
    },
    getFirstUnreadMessageId: (state) => (sectionId) => {
        let found = state.messageCountList.find((el) => {
            return (sectionId && el.sectionId == sectionId)
        })
        if (!found) {
            return 0
        } else {
            return found.firstUnreadMessageId
        }
    },
    getMessageCountByPid: (state) => (projectId, onlyProjectCount = false) => {
        let count = 0
        if (onlyProjectCount) {
            state.messageCountList.map((el) => {
                if (!el.sectionId && el.projectId == projectId) {
                    count += el.messageCount
                }
            })
        } else {
            state.messageCountList.map((el) => {
                if (el.projectId == projectId) {
                    count += el.messageCount
                }
            })
        }
        return count
    },
    getMessageMentionings: (state) => state.messageMentionings
}

const actions = {
    setActiveChat({commit}, data) {
        data.messageId = data.messageId ? data.messageId : null
        commit('SET_ACTIVE_CHAT', data)
    },
    setMessageCount({commit}, data) {
        commit('SET_MESSAGE_COUNT', data)
    },
    fetchMessageMentionings({commit}, projectId) {
        return new Promise((resolve, reject) => {
            axios.get('/rest/project/' + projectId + '/mentionings').then((response) => {
                commit('SET_MESSAGE_MENTIONINGS', response.data)
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    pushMessageMentioning({commit}, messageMentioning) {
        commit('ADD_MESSAGE_MENTIONING', messageMentioning)
    },
    markMessageMentioningAsRead({commit}, messageMentioningId) {
        axios.get('/rest/messages/mentioning/' + messageMentioningId + '/markAsRead')
        commit('REMOVE_MESSAGE_MENTIONING', messageMentioningId)
    },
    markAllMessagesAsRead({commit}, projectId) {
        return new Promise((resolve, reject) => {
            axios.get('/rest/messages/' + projectId + '/markAllSectionMessagesAsRead').then((response) => {
                commit('MARK_SECTION_MESSAGES_AS_READ')
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
    markAllAsReadXapi({}, projectId) {
        return new Promise((resolve, reject) => {
            axios.get('/rest/xapi/project/' + projectId + '/markAllAsRead').then((response) => {
                resolve(response)
            }, (e) => {
                console.error(e)
                reject(e)
            })
        })
    },
}

const mutations = {
    SET_ACTIVE_CHAT: (state, data) => state.activeChat = data,
    SET_MESSAGE_COUNT: (state, data) => {
        let found = state.messageCountList.find((el) => {
            return el.sectionId == data.sectionId || (!el.sectionId && el.projectId == data.projectId)
        })
        if (!found) {
            state.messageCountList.push({
                sectionId: data.sectionId,
                projectId: data.projectId,
                messageCount: data.messageCount,
                userWasInvolvedInSection: data.userWasInvolvedInSection,
                firstUnreadMessageId: data.firstUnreadMessageId
            })
        } else {
            found.messageCount = data.messageCount
            // In this case we want to reset the readMessageId
            if (!data.firstUnreadMessageId) {
                found.firstUnreadMessageId = null
            } else {
                // Only set a new firstUnreadMessageId if there is not a older one
                if (!found.firstUnreadMessageId) {
                    found.firstUnreadMessageId = data.firstUnreadMessageId
                }
            }
        }
    },
    SET_MESSAGE_MENTIONINGS: (state, data) => state.messageMentionings = data,
    ADD_MESSAGE_MENTIONING: (state, messageMentioning) => (state.messageMentionings.push(messageMentioning)),
    REMOVE_MESSAGE_MENTIONING: (state, messageMentioningId) => (state.messageMentionings.splice(state.messageMentionings.findIndex(el => el.id === messageMentioningId), 1)),
    MARK_SECTION_MESSAGES_AS_READ: (state) => {
        state.messageCountList
            .filter(item => item.sectionId !== null)
            .map(item => item.messageCount = 0)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
