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
                    :id="'data-num-' + index"
                >
                    <div
                        class="stats-box-data-num"
                        :style="this.isMobile ?
                            {fontSize: 'clamp(1px,'+ status.fontSize +'px,1.05rem)'} :
                            {fontSize: 'clamp(1px,'+ status.fontSize +'px,1.2rem)'}
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
        <div class="stats-box achievements-box">
            <div class="stats-box-title">
                実績
            </div>
            <achievement-icons class="achievement-icons" :achievement_data="store.achievements"></achievement-icons>
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
import AchievementIcons from "./AchievementIcons.vue";

export default defineComponent({
    components: {
        AchievementIcons
    },
    data() {
        return {
            statuses: [] as {
                title: string,
                numText: string,
                unit: string,
                fontSize: number,
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
                fontSize: 1
            },
            {
                title: "人口",
                numText: this.store.status.population.toLocaleString(),
                unit: "人",
                fontSize: 1
            },
            {
                title: "資金",
                numText: this.store.status.funds.toLocaleString(),
                unit: "億円",
                fontSize: 1
            },
            {
                title: "食料",
                numText: this.store.status.foods.toLocaleString(),
                unit: "㌧",
                fontSize: 1
            },
            {
                title: "資源",
                numText: this.store.status.resources.toLocaleString(),
                unit: "㌧",
                fontSize: 1
            },
            {
                title: "環境",
                numText: this.store.getEnvironmentString,
                unit: "",
                fontSize: 1
            },
            {
                title: "面積",
                numText: this.store.status.area.toLocaleString(),
                unit: "万坪",
                fontSize: 1
            },
            {
                title: "農業",
                numText: this.store.status.foods_production_capacity.toLocaleString(),
                unit: "人規模",
                fontSize: 1
            },
            {
                title: "工業",
                numText: this.store.status.funds_production_capacity.toLocaleString(),
                unit: "人規模",
                fontSize: 1
            },
            {
                title: "資源生産",
                numText: this.store.status.resources_production_capacity.toLocaleString(),
                unit: "人規模",
                fontSize: 1
            },
        ]

        this.$nextTick(() => {
            this.calcNumFontSizes();
        })
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
        },
    },
    methods: {
        calcNumFontSizes() {
            this.statuses.forEach((stat, index) => {
                const w = document.getElementById("data-num-" + index).clientWidth;
                console.debug(w);
                if(this.screenWidth < 768) { // Tailwind md:
                    stat.fontSize = w / (stat.numText.length*0.7);
                } else if(this.screenWidth < 1024) {
                    stat.fontSize = w / (stat.numText.length*0.5);
                } else {
                    stat.fontSize = 124 / (stat.numText.length*0.5);
                }
            })
        },
        onWindowSizeChanged() {
            const newScreenWidth = document.documentElement.clientWidth;
            if (this.screenWidth != newScreenWidth) {
                this.isMobile = (document.documentElement.clientWidth < 1024);
                this.calcNumFontSizes()
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

    .achievements-box {
        @apply col-span-3 md:col-span-2 z-10;

        .achievement-icons {
            @apply w-fit max-w-full mx-auto;
        }
    }

    .comment-box {
        @apply col-span-3;

        .island-comment {
            @apply px-2 md:px-4 my-1 text-left leading-none;
        }
    }
}
</style>
