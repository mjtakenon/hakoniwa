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

        <div class="subtitle">
            {{ store.island.name }}島の近況
        </div>
        <div class="log-texts flex items-start" v-for="(log, logIndex) of logs" :key="'log-' + logIndex">

            <div class="my-1 border-r border-gray-300">
                <div class="w-fit mr-2 text-center">
                    <span class="block text-gray-500 text-xs">ターン</span>
                    <span class="block -mt-1 font-bold text-lg">{{log.turn}}</span>
                </div>
            </div>
            <div class="grow">
                <div
                    v-for="(line, lineIndex) of log.texts"
                    :key="'line-' + logIndex + '-' + lineIndex"
                    class="pl-2"
                >
                    <span>・</span>
                    <template
                        v-for="(context, conIndex) of line"
                        :key="'text-' + logIndex + '-' + lineIndex + '-' + conIndex"
                    >
                        <a v-if="context.hasOwnProperty('link')" :href="context.link" :style="context.style">
                            {{context.text}}
                        </a>
                        <span v-else :style="context.style">
                            {{context.text}}
                        </span>
                    </template>
                </div>
                <div class="mt-auto grid grid-cols-5 gap-2 pl-[20vw] text-left">
                    <div class="w-full flex items-center px-0.5 border-b-2" :class="getBgColor(log.diff.foods)">
                        <span class="gray-600 text-[6px] mr-1">🍎食料</span>
                        <span class="text-sm font-bold grow text-right" :class="getTextColor(log.diff.foods)">
                            {{ log.diff.foods > 0 ? "+" + log.diff.foods : log.diff.foods}}
                        </span>
                        <span class="gray-600 text-[6px]">㌧</span>
                    </div>

                    <div class="w-full flex items-center px-0.5 border-b-2" :class="getBgColor(log.diff.funds)">
                        <span class="gray-600 text-[6px] mr-1">💰資金</span>
                        <span class="text-sm font-bold grow text-right" :class="getTextColor(log.diff.funds)">
                            {{ log.diff.funds > 0 ? "+" + log.diff.funds : log.diff.funds}}
                        </span>
                        <span class="gray-600 text-[6px]">億円</span>
                    </div>

                    <div class="w-full flex items-center px-0.5 border-b-2" :class="getBgColor(log.diff.resources)">
                        <span class="gray-600 text-[6px] mr-1">🏭資源</span>
                        <span class="text-sm font-bold grow text-right" :class="getTextColor(log.diff.resources)">
                            {{ log.diff.resources > 0 ? "+" + log.diff.resources : log.diff.resources}}
                        </span>
                        <span class="gray-600 text-[6px]">㌧</span>
                    </div>

                    <div class="w-full flex items-center px-0.5 border-b-2" :class="getBgColor(log.diff.population)">
                        <span class="gray-600 text-[6px] mr-1">👤人口</span>
                        <span class="text-sm font-bold grow text-right" :class="getTextColor(log.diff.population)">
                            {{ log.diff.population > 0 ? "+" + log.diff.population : log.diff.population}}
                        </span>
                        <span class="gray-600 text-[6px]">人</span>
                    </div>

                    <div class="w-full flex items-center px-0.5 border-b-2" :class="getBgColor(log.diff.points)">
                        <span class="gray-600 text-[6px] mr-1">⚡ポイント</span>
                        <span class="text-sm font-bold grow text-right" :class="getTextColor(log.diff.points)">
                            {{ log.diff.points > 0 ? "+" + log.diff.points : log.diff.points}}
                        </span>
                        <span class="gray-600 text-[6px]">pts</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
import {defineComponent} from "vue";
import {useMainStore} from "../store/MainStore";
import {LogParser, NewLog} from "../store/Entity/NewLog";

export default defineComponent({
    data() {
        return {
            logs: [] as NewLog[]
        }
    },
    setup() {
        const store = useMainStore();
        return { store };
    },
    mounted() {
        const logs = this.store.logs;
        const parser = new LogParser();
        this.logs = parser.parse(logs);
    },
    methods: {
        getBgColor(num: number): string {
            if (num > 0) return 'border-blue-200'
            if (num === 0) return 'border-gray-300'
            return 'border-red-200'
        },
        getTextColor(num: number): string {
            if (num > 0) return 'text-blue-600'
            if (num === 0) return 'text-black'
            return 'text-red-600'
        }
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
