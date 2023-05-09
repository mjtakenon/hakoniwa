<template>
    <div id="logs">
        <div class="subtitle">
            {{ store.island.name }}島の近況
        </div>
        <div class="w-full" v-for="(log, index) of store.logs" :key="'log-' + index">
            <span v-for="context of JSON.parse(log.log)" :key="context.text">
                <a v-if="context.hasOwnProperty('link')" :href="context.link" :style="context.style">
                    {{ context.text }}
                </a>
                <span v-else :style="context.style">
                    {{ context.text }}
                </span>
            </span>
        </div>
    </div>
</template>
<script lang="ts">
import {defineComponent} from "vue";
import {useMainStore} from "../store/MainStore";

export default defineComponent({
    setup() {
        const store = useMainStore();
        return { store };
    }
});
</script>

<style lang="postcss" scoped>

#logs {
    @apply text-left w-full px-3 mb-10
}

</style>
