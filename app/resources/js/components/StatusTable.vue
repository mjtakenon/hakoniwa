<template>
    <div class="stats">
        <div class="stats-header">
            <div class="names">
                <h1 class="island-name">{{ store.island.name }}島</h1>
                <span class="owner-name">({{ store.island.owner_name }})</span>
            </div>
            <div class="header-achievements">
                <achievement-icons :achievement_data="store.achievements"></achievement-icons>
            </div>
        </div>
        <div class="stats-contents">
            <div class="stats-island">
                <div class="data-point stat-box-island border-b">
                    <div class="stat-box-title">発展ポイント</div>
                    <div class="stat-inner">
                        <div class="stat-box-num">
                            {{ store.status.development_points.toLocaleString() }}
                        </div>
                        <div class="stat-box-unit">
                            pts
                        </div>
                    </div>
                </div>
                <div class="data-area stat-box-island border-b">
                    <div class="stat-box-title">面積</div>
                    <div class="stat-inner">
                        <div class="stat-box-num">
                            {{ store.status.area.toLocaleString() }}
                        </div>
                        <div class="stat-box-unit">
                            万坪
                        </div>
                    </div>
                </div>
                <div class="data-environment stat-box-island border-b">
                    <div class="stat-box-title">環境</div>
                    <div class="stat-inner environment">
                        <div class="stat-box-num">
                            {{ store.getEnvironmentString }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="stats-info">
                <div class="stats-summary">
                    <div class="stats-subtitle">島の情報</div>
                    <div class="stats-summary-inner">
                        <div class="stat-box-info">
                            <font-awesome-icon icon="fa-solid fa-sack-dollar" class="stat-box-icon"/>
                            <div class="stat-box-title">資金</div>
                            <div class="stat-box-num">{{ store.status.funds.toLocaleString() }}</div>
                            <div class="stat-box-unit">億円</div>
                        </div>
                        <div class="stat-box-info">
                            <font-awesome-icon icon="fa-solid fa-wheat-awn" class="stat-box-icon"/>
                            <div class="stat-box-title">食料</div>
                            <div class="stat-box-num">{{ store.status.foods.toLocaleString() }}</div>
                            <div class="stat-box-unit">㌧</div>
                        </div>
                        <div class="stat-box-info">
                            <font-awesome-icon icon="fa-solid fa-oil-well" class="stat-box-icon"/>
                            <div class="stat-box-title">資源</div>
                            <div class="stat-box-num">{{ store.status.resources.toLocaleString() }}</div>
                            <div class="stat-box-unit">㌧</div>
                        </div>
                    </div>
                </div>
                <div class="stats-human">
                    <div class="stats-subtitle">人的資源</div>
                    <div class="stats-human-inner">
                        <div class="stats-human-left">
                            <div class="stat-box-human-left population">
                                <div class="stat-box-title">総人口</div>
                                <div class="stat-inner">
                                    <div class="stat-box-num">
                                        {{ store.status.population.toLocaleString() }}
                                    </div>
                                    <div class="stat-box-unit">
                                        人
                                    </div>
                                </div>
                            </div>
                            <div class="stat-box-human-left unassigned"
                                :class="{'plus': calcUnassigned > 0}"
                            >
                                <div class="stat-box-title">未割当</div>
                                <div class="stat-inner">
                                    <div class="stat-box-num">
                                        {{calcUnassigned.toLocaleString()}}
                                    </div>
                                    <div class="stat-box-unit">
                                        人
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="stats-human-right">
                            <div class="stat-box-info human">
                                <font-awesome-icon icon="fa-solid fa-wheat-awn" class="stat-box-icon"/>
                                <div class="stat-box-title">農業</div>
                                <div class="stat-box-num">
                                    {{ store.status.foods_production_capacity.toLocaleString() }}
                                </div>
                                <div class="stat-box-unit">人</div>
                            </div>
                            <div class="stat-box-info human">
                                <font-awesome-icon icon="fa-solid fa-sack-dollar" class="stat-box-icon"/>
                                <div class="stat-box-title">工業</div>
                                <div class="stat-box-num">
                                    {{ store.status.funds_production_capacity.toLocaleString() }}
                                </div>
                                <div class="stat-box-unit">人</div>
                            </div>
                            <div class="stat-box-info human">
                                <font-awesome-icon icon="fa-solid fa-oil-well" class="stat-box-icon"/>
                                <div class="stat-box-title">資源生産</div>
                                <div class="stat-box-num">
                                    {{ store.status.resources_production_capacity.toLocaleString() }}
                                </div>
                                <div class="stat-box-unit">人</div>
                            </div>
                            <div class="stat-box-info human">
                                <font-awesome-icon icon="fa-solid fa-shield" class="stat-box-icon"/>
                                <div class="stat-box-title">軍事</div>
                                <div class="stat-box-num">
                                    {{ store.status.maintenance_number_of_people.toLocaleString() }}
                                </div>
                                <div class="stat-box-unit">人</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="stats-comments">
            <!-- TODO:やる -->
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import {useMainStore} from "../store/MainStore";
import AchievementIcons from "./AchievementIcons.vue";
import {storeToRefs} from "pinia";
import {library} from "@fortawesome/fontawesome-svg-core";
import {faOilWell, faSackDollar, faShield, faWheatAwn} from "@fortawesome/free-solid-svg-icons";

export default defineComponent({
    components: {
        AchievementIcons
    },
    data() {
        return {
            statuses: [] as {
                title: string,
                numText: string,
                unit: string
            }[],
            isMobile: (document.documentElement.clientWidth < 1024),
            screenWidth: document.documentElement.clientWidth
        }
    },
    setup() {
        library.add(faSackDollar, faWheatAwn, faOilWell, faShield)

        const store = useMainStore();
        const {status: statusRef} = storeToRefs(store);
        return {store, statusRef};
    },
    mounted() {
        window.addEventListener("resize", this.onWindowSizeChanged);
    },
    unmounted() {
        window.removeEventListener("resize", this.onWindowSizeChanged);
    },
    computed: {
        hasComment() {
            return this.store.island.comment === null || this.store.island.comment === undefined || this.store.island.comment === "";
        },
        islandComment() {
            if (this.hasComment) {
                return "コメントはありません"
            } else {
                return this.store.island.comment;
            }
        },
        calcUnassigned() {
            return this.store.status.population -
                this.store.status.foods_production_capacity -
                this.store.status.funds_production_capacity -
                this.store.status.resources_production_capacity -
                this.store.status.maintenance_number_of_people;
        },
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
    @apply container mx-auto w-full bg-surface text-on-surface drop-shadow-md rounded-lg mb-4;
    @apply md:py-2 md:px-4;

    .stats-header {
        @apply w-full py-3 mb-4 border-b-2 border-dashed;
        @apply md:flex;

        .names {
            @apply grow flex items-end justify-center min-w-0;
            @apply max-md:w-full;

            .island-name {
                @apply font-bold text-2xl mr-2;
            }

            .owner-name {
                @apply text-on-surface-variant text-sm;
            }
        }

        .header-achievements {
            @apply w-full;
            @apply md:w-fit md:border-l md:min-w-[20%];
        }
    }

    .stats-contents {
        @apply w-full;
        @apply max-md:px-2;

        .stat-box-island {
            @apply py-1;

            .stat-box-title {
                @apply text-on-surface-variant text-sm text-left font-bold leading-none;
            }

            .stat-inner {
                @apply flex flex-wrap text-right items-end mt-auto;

                &.environment {
                    @apply mt-2;
                }

                .stat-box-num {
                    @apply grow min-w-0 text-on-surface text-right font-bold;
                    @apply text-sm sm:text-lg;
                    @apply max-md:w-full max-md:px-2 max-md:mt-auto leading-none;
                    @apply md:text-xl md:leading-none;
                }

                .stat-box-unit {
                    @apply ml-2 text-on-surface-variant leading-none;
                    @apply max-md:w-full max-md:text-xs max-md:leading-none;
                }
            }
        }

        .stat-box-info {
            @apply flex items-center py-1 px-2 rounded-lg bg-surface-variant text-on-surface-variant mb-1.5;
            @apply max-md:w-1/3 max-md:flex-wrap;
            @apply md:gap-2;

            &.human {
                @apply max-md:w-full;
            }

            .stat-box-icon {
                @apply text-sm;
                @apply md:text-xl;
            }

            .stat-box-title {
                @apply font-bold;
                @apply text-xs;
                @apply md:text-sm;
            }

            .stat-box-num {
                @apply grow min-w-0 text-right font-bold;
                @apply max-md:w-full max-md:px-1;
                @apply text-sm sm:text-lg sm:leading-none md:text-lg md:leading-none;
            }

            .stat-box-unit {
                @apply mt-auto text-right;
                @apply max-md:w-full text-[0.5rem] leading-none;
                @apply md:text-xs md:w-[24px];
            }
        }

        .stat-box-human-left {
            @apply flex flex-wrap py-1 px-2 w-full grow rounded-lg mb-2;

            .stat-box-title {
                @apply w-full text-on-surface-variant text-sm text-left font-bold leading-none;
            }

            .stat-inner {
                @apply flex flex-wrap gap-0 items-end w-full pb-2;

                .stat-box-num {
                    @apply grow min-w-0 text-on-surface text-right font-bold text-xl leading-none;
                    @apply max-md:w-full max-md:px-2 max-md:mt-auto text-lg leading-none;
                    @apply text-lg md:text-xl;
                }

                .stat-box-unit {
                    @apply ml-2 text-on-surface-variant leading-none text-right;
                    @apply max-md:w-full max-md:text-xs max-md:leading-none;
                }
            }

            &.population {
                @apply bg-primary;

                .stat-box-title, .stat-box-unit {
                    @apply text-on-primary;
                }

                .stat-box-num {
                    @apply text-surface;
                }
            }

            &.unassigned {
                @apply bg-primary-container;

                .stat-box-title, .stat-box-unit {
                    @apply text-on-primary-container;
                }

                .stat-box-num {
                    @apply text-on-surface;
                }

                &.plus {
                    @apply bg-minus dark:bg-on-minus text-on-minus dark:text-minus;

                    .stat-box-title, .stat-box-unit, .stat-box-num {
                        @apply text-on-minus dark:text-minus;
                    }
                }
            }
        }

        .stats-island {
            @apply flex;
            @apply gap-3 mb-2;
            @apply md:gap-8 md:mb-6;

            .data-point {
                @apply w-2/5;
                @apply md:w-1/2;
            }

            .data-area {
                @apply w-2/5;
                @apply md:w-1/4;
            }

            .data-environment {
                @apply w-1/5;
                @apply md:w-1/4;
            }
        }

        .stats-info {
            @apply flex;
            @apply max-md:flex-wrap;
            @apply md:gap-10;

            .stats-subtitle {
                @apply text-left font-bold text-on-surface-variant text-sm;
                @apply max-md:w-full;
            }

            .stats-summary {
                @apply w-full;
                @apply md:w-1/3;

                .stats-summary-inner {
                    @apply max-md:flex max-md:gap-1;
                }
            }

            .stats-human {
                @apply w-full;
                @apply md:w-2/3;

                .stats-human-inner {
                    @apply flex;
                    @apply gap-2;
                    @apply md:gap-4;

                    .stats-human-left {
                        @apply md:flex md:flex-wrap
                    }

                    .stats-human-left, .stats-human-right {
                        @apply w-1/2;
                    }
                }
            }
        }
    }
}
</style>
