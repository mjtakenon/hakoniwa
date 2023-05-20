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
            </div>
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
                return "text-base"
            }
        }
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
                abandoned_turn: number
            }>
        },
    }
});
</script>

<style lang="scss" scoped>
.ranking {
    @apply flex flex-wrap mb-3 p-0 rounded-xl border bg-surface drop-shadow-md text-on-surface;

    .ranking-index {
        @apply px-3 inline-flex items-center border-on-surface-variant;
        @apply max-md:w-full max-md:py-2;
        @apply max-md:border-b-2 md:border-r-2 md:w-1/4;

        .ranking-index-num {
            @apply font-black text-2xl mr-3;
        }

        .ranking-index-name {
            @apply grow font-black text-center text-on-link;
        }
    }

    .ranking-summary {
        @apply grow pl-1 pr-3 py-1 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5;

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
}



</style>
