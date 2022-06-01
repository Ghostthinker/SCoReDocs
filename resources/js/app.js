/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from "vue"

Vue.config.devtools = true
import vuetify from "./plugins/vuetify"
import router from "./plugins/router"
import MagicGrid from 'vue-magic-grid'
import store from './store/'
import checkView from 'vue-check-view'
import Axios from 'axios'
import VueHtml2Canvas from 'vue-html2canvas'

require('./bootstrap')
Vue.use(VueHtml2Canvas)

Vue.prototype.$userId = document.querySelector("meta[name='user-id']").getAttribute('content')
Vue.prototype.$userName = document.querySelector("meta[name='user-name']").getAttribute('content')
Vue.prototype.$userPicture = document.querySelector("meta[name='user-picture']").getAttribute('content')


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

Vue.component('media', require('./views/Media.vue').default)
Vue.component('app', require('./App.vue').default)
Vue.component('scorefooter', require('./components/footer/CustomScoreFooter.vue').default)
Vue.component('privacy', require('./components/privacy/PrivacyPolicy.vue').default)
Vue.component('terms', require('./components/privacy/TermsOfUse.vue').default)
Vue.component('help', require('./components/support/HelpDialog.vue').default)
Vue.component('notification', require('./components/notifications/Notification.vue').default)
Vue.use(MagicGrid)
Vue.use(checkView)
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
window.eventBus = new Vue({
    data: {
        eventListenerInsertedAnnotation: false,
        eventListenerInsertedSequence: false,
        eventListenerVideoUploadDone: false,
        eventListenerClick: false,
        eventListenerImgHover: false,
        activeCkEditorId: null,
        videoUploadDone: false
    }
})
Axios.defaults.headers = {
    'Cache-Control': 'no-cache',
    'Pragma': 'no-cache',
    'Expires': '0',
    accept : 'application/json',
}
Axios.interceptors.response.use(function (response) {
    return response
}, function (error) {
    switch (error.response.status) {
        case 401:
            error.response.data.message = 'Sie wurden ausgeloggt. Bitte melden Sie sich erneut an.'
            store.dispatch('notifications/persistentNotification', {
                message: error.response.data.message,
                type: 'error'
            })
            break
        case 419:
            error.response.data.message = 'Sie wurden ausgeloggt. Bitte melden Sie sich erneut an.'
            store.dispatch('notifications/persistentNotification', {
                message: error.response.data.message,
                type: 'error'
            })
            break
        default:
            console.error(error)
            break
    }
    return Promise.reject(error)
})

router.beforeEach(async (to, from, next) => {
    const { authorize } = to.meta
    if (authorize) {
        let currentUser
        await store.dispatch('user/fetchUser').then((user) => currentUser = user)
        // check if route is restricted by permissions
        if (!currentUser.meta[authorize]) {
            // permissions not authorised so redirect to home page
            return next({ path: '/' })
        }
    }

    next()
})

const app = new Vue({
    el: '#app',
    router,
    store,
    vuetify
})

export default app
