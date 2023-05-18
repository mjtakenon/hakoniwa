<template v-if="logs.length > 0">
    <div id="logs">
        <div class="subtitle">
            {{ title }}
        </div>
        <div class="turn-log" v-for="(log, logIndex) of logs" :key="'log-' + logIndex">
            <div class="turn-title">
                <div class="turn-title-inner">
                    <span class="turn-title-inner-text">ターン</span>
                    <span class="turn-title-inner-number">
                        {{ log.turn }}
                    </span>
                </div>
            </div>
            <div class="data-box">
                <div
                    v-for="(line, lineIndex) of log.texts"
                    :key="'line-' + logIndex + '-' + lineIndex"
                    class="log"
                >
                    <div class="mr-1">・</div>
                    <div>
                        <template
                            v-for="(context, conIndex) of line"
                            :key="'text-' + logIndex + '-' + lineIndex + '-' + conIndex"
                        >
                            <a v-if="context.hasOwnProperty('link')" :href="context.link" :style="context.style">
                                {{ context.text }}
                            </a>
                            <span v-else-if="context.text !== ''" :style="context.style">
                                {{ context.text }}
                            </span>
                        </template>
                    </div>
                </div>
                <div v-if="log.hasOwnProperty('summary')" class="turn-summaries">
                    <div class="summary-box" :class="getBgColor(log.summary.foods)">
                        <span class="summary-box-title">🍎食料</span>
                        <span class="summary-box-num" :class="getTextColor(log.summary.foods)">
                            {{
                                log.summary.foods > 0 ? "+" + log.summary.foods.toLocaleString() : log.summary.foods.toLocaleString()
                            }}
                        </span>
                        <span class="summary-box-unit">㌧</span>
                    </div>

                    <div class="summary-box" :class="getBgColor(log.summary.funds)">
                        <span class="summary-box-title">💰資金</span>
                        <span class="summary-box-num" :class="getTextColor(log.summary.funds)">
                            {{
                                log.summary.funds > 0 ? "+" + log.summary.funds.toLocaleString() : log.summary.funds.toLocaleString()
                            }}
                        </span>
                        <span class="summary-box-unit">億円</span>
                    </div>

                    <div class="summary-box" :class="getBgColor(log.summary.resources)">
                        <span class="summary-box-title">🏭資源</span>
                        <span class="summary-box-num" :class="getTextColor(log.summary.resources)">
                            {{
                                log.summary.resources > 0 ? "+" + log.summary.resources.toLocaleString() : log.summary.resources.toLocaleString()
                            }}
                        </span>
                        <span class="summary-box-unit">㌧</span>
                    </div>

                    <div class="summary-box" :class="getBgColor(log.summary.population)">
                        <span class="summary-box-title">👤人口</span>
                        <span class="summary-box-num" :class="getTextColor(log.summary.population)">
                            {{
                                log.summary.population > 0 ? "+" + log.summary.population.toLocaleString() : log.summary.population.toLocaleString()
                            }}
                        </span>
                        <span class="summary-box-unit">人</span>
                    </div>

                    <div class="summary-box" :class="getBgColor(log.summary.points)">
                        <span class="summary-box-title">⚡ポイント</span>
                        <span class="summary-box-num" :class="getTextColor(log.summary.points)">
                            {{
                                log.summary.points > 0 ? "+" + log.summary.points.toLocaleString() : log.summary.points.toLocaleString()
                            }}
                        </span>
                        <span class="summary-box-unit">pts</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
import {defineComponent, PropType} from "vue";
import {LogParser, Log, LogProps} from "../store/Entity/Log";

export default defineComponent({
    data() {
        return {
            logs: [] as Log[]
        }
    },
    methods: {
        getBgColor(num: number): string {
            if (num > 0) return 'border-plus'
            if (num === 0) return 'border-surface-variant'
            return 'border-minus'
        },
        getTextColor(num: number): string {
            if (num > 0) return 'text-on-plus'
            if (num === 0) return 'text-on-surface-variant'
            return 'text-on-minus'
        }
    },
    mounted() {
        if (this.unparsedLogs !== undefined) {
            const parser = new LogParser();
            this.logs = parser.parse(this.unparsedLogs);
        } else {
            this.logs = this.parsedLogs
        }
    },
    props: {
        title: {
            required: false,
            type: String
        },
        parsedLogs: {
            required: false,
            type: Array as PropType<Log[]>
        },
        unparsedLogs: {
            required: false,
            type: Array as PropType<LogProps[]>
        }
    }
});
</script>

<style lang="scss" scoped>

#logs {
    // general
    @apply text-left w-full mt-10 bg-surface pb-4 drop-shadow-md overflow-hidden;
    // desktop
    @apply lg:rounded-2xl;

    .subtitle {
        @apply mt-0 py-3 px-3 mb-3 w-full bg-primary text-on-primary font-bold;
    }

    .turn-log {
        // general
        @apply flex items-start;
        // sp
        @apply text-sm mb-1 max-md:mt-4 max-md:pb-6 max-md:flex-wrap;
        // desktop
        @apply md:border-b-2 md:border-b-surface-variant mt-4 md:text-base md:mx-5 lg:mb-0.5;

        .turn-title {
            // general
            @apply my-1;
            // sp
            @apply max-md:bg-surface-variant max-md:rounded-r-3xl max-md:w-2/5 max-md:px-2 max-md:py-1 max-md:drop-shadow;
            // desktop
            @apply md:border-r-2 md:border-surface-variant md:pr-2;

            .turn-title-inner {
                // general
                @apply text-center;
                // sp
                @apply w-full max-md:flex;
                // desktop
                @apply md:mr-2;

                .turn-title-inner-text {
                    // general
                    @apply text-xs text-on-surface-variant;
                    // desktop
                    @apply md:block;
                }

                .turn-title-inner-number {
                    // general
                    @apply mt-auto font-bold text-lg;
                    // sp
                    @apply max-md:grow max-md:text-center;
                    // desktop
                    @apply md:block md:-mt-1;
                }
            }
        }

        .data-box {
            // sp
            @apply max-md:mt-2 max-md:w-full;
            // desktop
            @apply md:grow;

            .log {
                @apply pl-2 flex;
            }

            .log:last-child {
                @apply mb-3;
            }

            .turn-summaries {
                // general
                @apply mt-2 grid gap-2 text-left;
                // sp
                @apply grid-cols-2 max-md:mt-3;
                // desktop
                @apply md:grid-cols-5 md:pl-[10%] lg:pl-[25%];

                .summary-box {
                    @apply w-full flex items-center px-0.5 border-b-2;

                    .summary-box-title {
                        @apply text-on-surface-variant text-[0.6rem] mr-1;
                    }

                    .summary-box-num {
                        @apply text-sm font-bold grow text-right;
                    }

                    .summary-box-unit {
                        @apply text-on-surface-variant text-[0.6rem] ml-1 text-right;
                    }
                }
            }
        }
    }

    .turn-log:last-child {
        @apply border-none;
    }
}

</style>
