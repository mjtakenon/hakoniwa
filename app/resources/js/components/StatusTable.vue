<template>
    <div class="stats">
        <div
            v-for="(status, index) in statuses"
            class="stats-box"
            :class="{'max-md:col-span-3': (index === 0)}"
            :key="status.title"
        >
            <div class="stats-box-title">
                {{ status.title }}
            </div>
            <div class="stats-box-data">
                <div
                    class="stats-box-num-wrapper"
                >
                    <div
                        class="stats-box-data-num"
                        :style="this.isMobile ?
                            {fontSize: 'clamp(1px, calc(100cqi/' + (status.numText.length*0.5) +'), 1.05rem)'} :
                            {fontSize: 'clamp(1px, calc(100cqi/' + (status.numText.length*0.5) +'), 1.2rem)'}
                        "
                    >
                        {{ status.numText }}
                    </div>
                </div>
                <div class="stat-box-data-unit">
                    {{ status.unit }}
                </div>
            </div>
        </div>
        <div class="stats-box comment-box">
            <div class="stats-box-title">
                コメント
            </div>
            <div class="island-comment" :class="{'text-sm text-on-surface-variant': hasComment}">
                {{ islandComment }}
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import {useMainStore} from "../store/MainStore";
import {has} from "lodash";

export default defineComponent({
    data() {
        return {
            statuses: [] as {
                title: string,
                numText: string,
                unit: string,
            }[],
            isMobile: (document.documentElement.clientWidth < 1024),
            screenWidth: document.documentElement.clientWidth
        }
    },
    setup() {
        const store = useMainStore();
        return {store};
    },
    mounted() {
        window.addEventListener("resize", this.onWindowSizeChanged);
        this.statuses = [
            {
                title: "発展ポイント",
                numText: this.store.status.development_points.toLocaleString(),
                unit: "pts",
            },
            {
                title: "人口",
                numText: this.store.status.population.toLocaleString(),
                unit: "人"
            },
            {
                title: "資金",
                numText: this.store.status.funds.toLocaleString(),
                unit: "億円"
            },
            {
                title: "食料",
                numText: this.store.status.foods.toLocaleString(),
                unit: "㌧"
            },
            {
                title: "資源",
                numText: this.store.status.resources.toLocaleString(),
                unit: "㌧"
            },
            {
                title: "環境",
                numText: this.store.getEnvironmentString,
                unit: ""
            },
            {
                title: "面積",
                numText: this.store.status.area.toLocaleString(),
                unit: "万坪"
            },
            {
                title: "農業",
                numText: this.store.status.foods_production_capacity.toLocaleString(),
                unit: "人規模"
            },
            {
                title: "工業",
                numText: this.store.status.funds_production_capacity.toLocaleString(),
                unit: "人規模"
            },
            {
                title: "資源生産",
                numText: this.store.status.resources_production_capacity.toLocaleString(),
                unit: "人規模"
            },
        ]
    },
    unmounted() {
        window.removeEventListener("resize", this.onWindowSizeChanged);
    },
    computed: {
        hasComment() {
            return this.store.island.comment === null　|| this.store.island.comment === undefined || this.store.island.comment === "";
        },
        islandComment() {
            if (this.hasComment) {
                return "コメントはありません"
            } else {
                return this.store.island.comment;
            }
        }
    },
    methods: {
        onWindowSizeChanged() {
            const newScreenWidth = document.documentElement.clientWidth;
            if (this.screenWidth != newScreenWidth) {
                this.isMobile = (document.documentElement.clientWidth < 1024);
            }
        },
    }
});
</script>

<style lang="scss" scoped>
.stats {
    @apply container px-2 py-1 md:mx-auto mb-3 md:mb-6 max-w-full grid grid-cols-3 md:grid-cols-5 gap-2;

    .stats-box {
        @apply bg-surface-variant rounded-xl p-2 drop-shadow-md;

        .stats-box-title {
            @apply font-bold text-left text-on-surface-variant text-xs lg:text-sm;
        }

        .stats-box-data {
            @apply flex items-end flex-wrap h-8;

            .stats-box-num-wrapper {
                container-type: inline-size;
                @apply max-lg:w-full lg:grow min-w-0 text-right;
            }

            .stats-box-data-num {
                @apply font-bold w-full inline;
            }

            .stat-box-data-unit {
                @apply max-lg:w-full max-lg:-mt-1.5 text-[0.6rem] lg:text-sm text-right lg:pl-2;
            }
        }
    }

    .comment-box {
        @apply col-span-3 md:col-span-5;

        .island-comment {
            @apply px-2 md:px-4 text-left leading-none;
        }
    }
}
</style>
