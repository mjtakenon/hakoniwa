import _ from 'lodash'

window._ = _

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios'

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios = axios

import { App } from 'vue'
import { createApp } from 'vue/dist/vue.esm-bundler'
import { createPinia } from 'pinia'
import Tres from '@tresjs/core'
import IslandsPage from '$vue/pages/Islands/IslandsPage.vue'
import PlansPage from '$vue/pages/Islands/Plans/PlansPage.vue'
import VueHeader from '$vue/layout/VueHeader.vue'
import SettingsPage from '$vue/pages/Settings/SettingsPage.vue'
import ReleaseNotes from '$vue/pages/Releases/ReleasesPage.vue'
import VueFooter from '$vue/layout/VueFooter.vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import ReleasesPage from '$vue/pages/Releases/ReleasesPage.vue'
import IndexPage from '$vue/pages/Index/IndexPage.vue'

export const app: App = createApp({
  components: {
    IndexPage,
    IslandsPage,
    PlansPage,
    VueHeader,
    VueFooter,
    ReleaseNotes,
    SettingsPage,
    ReleasesPage
  }
})

const pinia = createPinia()
app.use(pinia)
app.use(Tres)
app.component('FontAwesomeIcon', FontAwesomeIcon)
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
