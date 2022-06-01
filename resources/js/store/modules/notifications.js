const state = {
    notifications: [],
    id: 0
}

const getters = {
    getNotifications: (state) => state.notifications
}

const actions = {
    newNotification({ commit, state }, notification){
        const currentId = state.id

        commit('SET_NOTIFICATION', notification)

        setTimeout(function(){commit('REMOVE_NOTIFICATION', currentId)}, notification.duration || 5000)
    },
    persistentNotification({ commit, state }, notification){
        if(!state.notifications.find(not => not.name === notification.name)) {
            commit('SET_NOTIFICATION', notification)
        }
    },
    closeNotification({ commit }, index){
      commit('REMOVE_NOTIFICATION', index)
    }
}

const mutations = {
    SET_NOTIFICATION: (state, notification) => {
        notification.id = state.id
        state.notifications.push(notification)
        state.id++
    },
    REMOVE_NOTIFICATION: (state, id) => state.notifications.splice(state.notifications.findIndex(el => el.id === id), 1)
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
