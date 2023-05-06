<template>
    <a :href="'/islands/' + $props.island.id">
        <div class="ranking">
            <div class="ranking-index">
                <div class="ranking-index-num">{{ $props.index }}</div>
                <div class="ranking-index-name">{{ $props.island.name }}島</div>
            </div>
            <div class="ranking-summary">
                <div class="pl-1 pr-3 md:border-r-2">
                    <div class="ranking-summary-title">発展ポイント</div>
                    <div class="ranking-summary-data">
                        <div
                            class="ranking-summary-data-num">{{ $props.island.development_points }}
                        </div>
                        <div class="ranking-summary-data-unit">pts</div>
                    </div>
                </div>
                <div class="pl-1 md:pr-3 md:border-r-2">
                    <div class="ranking-summary-title">人口</div>
                    <div class="ranking-summary-data">
                        <div
                            class="ranking-summary-data-num">{{ $props.island.population }}
                        </div>
                        <div class="ranking-summary-data-unit">人</div>
                    </div>
                </div>
                <div class="pl-1 pr-3 md:border-r-2">
                    <div class="ranking-summary-title">資金</div>
                    <div class="ranking-summary-data">
                        <div
                            class="ranking-summary-data-num">{{ $props.island.funds }}
                        </div>
                        <div class="ranking-summary-data-unit">億円</div>
                    </div>
                </div>
                <div class="pl-1 md:pr-3 md:border-r-2">
                    <div class="ranking-summary-title">食料</div>
                    <div class="ranking-summary-data">
                        <div
                            class="ranking-summary-data-num">{{ $props.island.foods }}
                        </div>
                        <div class="ranking-summary-data-unit">㌧</div>
                    </div>
                </div>
                <div class="pl-1 pr-3">
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

<script>
export default {
    props: ['index', 'island']
}
</script>

<style lang="postcss" scoped>
.ranking {
    @apply flex flex-wrap mb-2 p-0 rounded-xl border-2 bg-gray-100 border-gray-300 drop-shadow-md text-black;

    .ranking-index {
        @apply px-3 inline-flex items-center border-gray-300;
        @apply max-md:w-full max-md:py-2;
        @apply max-md:border-b-2 md:border-r-2 md:w-1/4;

        .ranking-index-num {
            @apply font-black text-2xl mr-3;
        }

        .ranking-index-name {
            @apply grow font-black text-center text-lg text-link;
        }
    }

    .ranking-summary {
        @apply grow pl-1 pr-3 py-1 grid grid-cols-2 md:grid-cols-5;

        .ranking-summary-title {
            @apply font-bold text-gray-500 text-xs underline;
        }

        .ranking-summary-data {
            @apply flex items-end;

            .ranking-summary-data-num {
                @apply grow text-lg inline-block text-right font-bold mr-2;
            }

            .ranking-summary-data-unit {
                @apply text-xs w-1/6 text-right;
            }
        }
    }

}
</style>
