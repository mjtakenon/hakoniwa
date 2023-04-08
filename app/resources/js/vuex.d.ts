import { ComponentCustomProperties } from 'vue'
import { Store } from 'vuex'
import {State} from "./store";

declare module '@vue/runtime-core' {
    // `this.$store` の型付けを提供する
    interface ComponentCustomProperties {
        $store: Store<State>
    }
}
