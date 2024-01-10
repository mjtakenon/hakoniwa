import './bootstrap'
import DebugTools from '$vue/layout/DebugTools.vue'
import { app } from './bootstrap.js'

console.debug('---development mode---')
app.component('DebugTools', DebugTools)
app.mount('#app')
