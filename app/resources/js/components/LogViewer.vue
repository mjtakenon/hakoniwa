<template>
    <div id="logs">
        <div class="subtitle">
            {{ store.island.name }}島の近況
        </div>
        <div class="log-texts" v-for="(log, index) of store.logs" :key="'log-' + index">
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
    @apply text-left w-full mt-10 mb-10 bg-gray-100 pb-4 md:rounded-2xl drop-shadow-md overflow-hidden;

    .subtitle {
        @apply mt-0 py-3 px-3 border-b border-b-gray-300 mb-3 w-full bg-success-dark text-white;
    }

    .log-texts {
        @apply text-sm md:text-base mx-1 md:mx-5 mb-1 lg:mb-0.5 border-b border-b-gray-300;
    }

    .log-texts:last-child {
        @apply border-none;
    }
}

</style>
