<template>
    <a
        :href="'/islands/' + $props.island.id"
        :id="'ranking-' + $props.island.id"
        class="block"
        :class="[isAppeared ? 'animate-slide-in-left' : '']"
    >
        <div class="ranking">
            <div class="ranking-index">
                <div class="ranking-index-num">{{ $props.index }}</div>
                <div class="ranking-index-info">
                    <div v-if="$props.island.abandoned_turn >= 1"
                         class="ranking-index-name"
                         :class="islandNameSize"
                    >
                        {{ $props.island.name }}島 ({{ $props.island.abandoned_turn }})
                    </div>
                    <div v-else
                         class="ranking-index-name"
                         :class="islandNameSize"
                    >
                        {{ $props.island.name }}島
                    </div>
                    <div class="ranking-index-owner">
                        {{ $props.island.owner_name }}
                    </div>
                </div>

            </div>
            <div class="ranking-data">
                <div class="ranking-summary">
                    <div class="ranking-summary-wrapper">
                        <div class="ranking-summary-title">発展ポイント</div>
                        <div class="ranking-summary-data">
                            <div
                                class="ranking-summary-data-num">{{ $props.island.development_points }}
                            </div>
                            <div class="ranking-summary-data-unit">pts</div>
                        </div>
                    </div>
                    <div class="ranking-summary-wrapper">
                        <div class="ranking-summary-title">人口</div>
                        <div class="ranking-summary-data">
                            <div
                                class="ranking-summary-data-num">{{ $props.island.population }}
                            </div>
                            <div class="ranking-summary-data-unit">人</div>
                        </div>
                    </div>
                    <div class="ranking-summary-wrapper">
                        <div class="ranking-summary-title">資金</div>
                        <div class="ranking-summary-data">
                            <div
                                class="ranking-summary-data-num">{{ $props.island.funds }}
                            </div>
                            <div class="ranking-summary-data-unit">億円</div>
                        </div>
                    </div>
                    <div class="ranking-summary-wrapper">
                        <div class="ranking-summary-title">食料</div>
                        <div class="ranking-summary-data">
                            <div
                                class="ranking-summary-data-num">{{ $props.island.foods }}
                            </div>
                            <div class="ranking-summary-data-unit">㌧</div>
                        </div>
                    </div>
                    <div class="ranking-summary-wrapper">
                        <div class="ranking-summary-title">資源</div>
                        <div class="ranking-summary-data">
                            <div
                                class="ranking-summary-data-num">{{ $props.island.resources }}
                            </div>
                            <div class="ranking-summary-data-unit">㌧</div>
                        </div>
                    </div>
                </div>
                <div class="island-comment-box" :class="{'text-on-surface-variant': hasComment}">
                    <div class="comment-index">comment:</div>
                    <div class="comment-text">{{islandComment}}</div>
                </div>
            </div>

        </div>
    </a>
</template>

<script lang="ts">
import {defineComponent, PropType} from "vue";

export default defineComponent({
    data() {
        return {
            observer: null as IntersectionObserver,
            isAppeared: false // スクリーン上に出てきたかどうか
        }
    },
    mounted() {
        const target = document.querySelector("#ranking-" + this.island.id);

        this.observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) this.onAppeared();
            })
        });
        this.observer.observe(target);
    },
    computed: {
        islandNameSize(){
            const nameSize = this.$props.island.name.length
            if(nameSize > 24) {
                return "text-xs"
            }
            else if (nameSize > 16){
                return "text-sm"
            } else {
                return "text-lg"
            }
        },
        hasComment() {
            return this.$props.island.comment === null　|| this.$props.island.comment === undefined || this.$props.island.comment === "";
        },
        islandComment() {
            if (this.hasComment) {
                return "コメントはありません"
            } else {
                return this.$props.island.comment;
            }
        },
    },
    methods: {
        onAppeared() {
            this.isAppeared = true;
            this.observer.disconnect();
        }
    },
    props: {
        index: {
            required: true,
            type: Number
        },
        // TODO: ランキングのIslandデータ型は個別で定義しておいた方がよい？
        island: {
            required: true,
            type: Object as PropType<{
                id: number,
                name: string,
                owner_name: string,
                development_points: number,
                funds: number,
                foods: number,
                resources: number,
                population: number,
                funds_production_capacity: number,
                foods_production_capacity: number,
                resources_production_capacity: number,
                environment: string,
                area: number
                abandoned_turn: number,
                comment: string
            }>
        },
    }
});
</script>

<style lang="scss" scoped>
.ranking {
    @apply flex flex-wrap mb-3 p-0 rounded-xl border bg-surface drop-shadow-md text-on-surface overflow-hidden;

    .ranking-index {
        @apply px-3 inline-flex items-center;
        @apply max-md:w-full max-md:py-2;
        @apply max-md:border-b-2 md:border-r-2 md:min-w-[25%] md:max-w-[25%];

        .ranking-index-num {
            @apply font-black text-2xl mr-3;
        }

        .ranking-index-info {
            @apply w-full text-center;

            .ranking-index-name {
                @apply grow font-black text-center text-on-link;
            }

            .ranking-index-owner {
                @apply text-xs leading-none text-on-surface-variant;
            }
        }
    }

    .ranking-data {
        @apply grow pt-1 md:max-w-[75%];

        .ranking-summary {
            @apply grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5;

            .ranking-summary-title {
                @apply font-bold text-on-surface-variant text-xs underline;
            }

            .ranking-summary-wrapper {
                @apply pl-1 pr-3 md:border-r;
            }

            .ranking-summary-wrapper:nth-of-type(3) {
                @apply md:max-lg:border-none;
            }
            .ranking-summary-wrapper:nth-of-type(5) {
                @apply md:border-none;
            }

            .ranking-summary-data {
                @apply flex items-end;

                .ranking-summary-data-num {
                    @apply grow text-base md:text-sm lg:text-lg inline-block text-right font-bold mr-2;
                }

                .ranking-summary-data-unit {
                    @apply text-xs w-1/6 text-right;
                }
            }
        }

        .island-comment-box {
            // general
            @apply w-full border-t border-dashed px-2;
            // sp
            @apply max-md:my-2 pt-3;
            // desktop
            @apply md:py-1 md:mt-1;

            .comment-index {
                @apply text-xs text-on-surface-variant leading-none;
            }

            .comment-text {
                @apply text-sm leading-none text-on-surface;
            }
        }
    }
}



</style>
