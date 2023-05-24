import './bootstrap'
import DebugTools from "./components/DebugTools.vue";
import {app} from "./bootstrap";

console.debug("---development mode---");
app.component('DebugTools', DebugTools);
app.mount("#app");
