<template>
    <div id="island">
        <div
            class="row"
            v-for="y of $store.state.hakoniwa.height"
            :key="y"
        >
            <div class="right-padding" :class="{'opacity-80': this.showPlanWindow}" v-if="y%2 === 1">
                <span class="right-padding-text">{{ y-1 }}</span>
            </div>
            <div class="cell" v-for="x of $store.state.hakoniwa.width" :key="x">
                <img
                    @mouseover="onMouseOverCell(x-1, y-1, $event)"
                    @mouseleave="onMouseLeaveCell()"
                    @click="onClickCell(x-1, y-1, $event)"
                    :src="getIslandTerrain(x-1,y-1).data.image_path"
                    :alt="getIslandTerrain(x-1,y-1).data.info"
                    :class="[
                    'cell',
                    isSelectedCell(x-1, y-1) && this.showPlanWindow ? 'cell-is-selected' : '',
                    !isSelectedCell(x-1, y-1) && this.showPlanWindow ? 'opacity-80' : '',
                    isReferencedCell(x-1, y-1) ? 'cell-is-referenced' : '',
                ]"
                >
            </div>
            <div class="left-padding" :class="{'opacity-80': this.showPlanWindow}" v-if="y%2 === 0"></div>
        </div>
        <div v-show="showHoverWindow" class="hover-window" :style="{ bottom: hoverWindowY+'px', left: hoverWindowX+'px' }">
            <div class="hover-window-header">
                <img
                    class="hover-window-img"
                    :src="getIslandTerrain(hoverCellPoint.x, hoverCellPoint.y).data.image_path"
                >
                <div class="grow items-center hover-window-info">
                    {{ (getIslandTerrain(hoverCellPoint.x, hoverCellPoint.y).data.info) }}
                </div>
            </div>
                    <template v-for="(plan, index) of $store.state.plans">
                        <div class="hover-window-plan" v-if="
                            plan.data.usePoint &&
                            plan.data.point.x === hoverCellPoint.x &&
                            plan.data.point.y === hoverCellPoint.y &&
                            (!plan.data.useTargetIsland || plan.data.useTargetIsland && plan.data.targetIsland === $store.state.island.id)
                        ">
                            <span>[{{ index + 1 }}] </span>
                            <span>{{ plan.data.name }}</span>
                            <span v-if="plan.data.isFiring">
                                <span v-if="plan.data.amount >= 2 && plan.data.useAmount"> ({{ plan.data.amount }}発発射)</span>
                                <span v-else-if="plan.data.amount === 0 && plan.data.useAmount"> (無制限)</span>
                            </span>
                            <span v-else>
                                <span v-if="plan.data.amount >= 2 && plan.data.useAmount"> ({{ plan.data.amount }}回実施)</span>
                            </span>
                        </div>
                    </template>

        </div>
        <div v-show="showPlanWindow" class="plan-window"
             :style="[
                 { top: planWindowY + 'px' },
                 !isMobile || planWindowLeftSide ? { left: planWindowX + 'px' } : { right: planWindowX + 'px' }
             ]"
        >
            <div class="plan-window-header">
                <div class="grow px-3">
                    <span class="mr-2">({{$store.state.selectedPoint.x}},{{$store.state.selectedPoint.y}})</span>
                    <span class="text-xs">計画番号: </span>
                    <span class="mr-1">{{$store.state.selectedPlanNumber}}</span>
                </div>

                <button
                    class="inline-block mr-3"
                    @click="onClickClosePlan"
                >×</button>
            </div>
            <div
                v-for="plan of $store.state.planCandidate"
                :key="plan.key"
                class="plan-window-select"
            >
                <a @click="onClickPlan(plan.key)">
                        <span class="action-name">{{ plan.name }}</span>
                        <span class="action-price">{{ plan.priceString }}</span>
                </a>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Terrain } from "../store/Entity/Terrain";
import {Plan} from "../store/Entity/Plan";

export default {
    components: {},
    data() {
        return {
            MAX_PLAN_NUMBER: 30,
            showHoverWindow: false,
            showPlanWindow: false,
            hoverCellPoint: {
                "x": 0,
                "y": 0,
            },
            hoverWindowY: 170,
            hoverWindowX: 0,
            screenWidth: document.documentElement.clientWidth,
            planWindowY: 0,
            planWindowX: 0,
            planWindowLeftSide: true,
            isMobile: (document.documentElement.clientWidth < 1024),
        }
    },
    setup() {
    },
    mounted() {
        window.addEventListener("resize", this.onWindowSizeChanged);
    },
    unmounted() {
        window.removeEventListener("resize", this.onWindowSizeChanged);
    },
    methods: {
        getIslandTerrain(x, y): Terrain {
            return this.$store.state.terrains.filter(function(item, idx){
                if (item.data.point.x === x && item.data.point.y === y) return true;
            }).pop();
        },
        onMouseOverCell(x, y, event: MouseEvent) {
            const offsetY = 25;
            this.hoverWindowY = document.documentElement.clientHeight - event.pageY + offsetY;
            this.hoverWindowX = event.pageX;

            // Screen Overflow Check
            if(this.isMobile) {
                const windowSize = 200;
                const paddingOffset = 20;
                const leftEdge = this.hoverWindowX - (windowSize/2);
                const rightEdge = this.hoverWindowX + (windowSize/2);
                if (leftEdge < paddingOffset) {
                    this.hoverWindowX += (-leftEdge) + paddingOffset;
                }
                else if (rightEdge > this.screenWidth) {
                    this.hoverWindowX -= (rightEdge-this.screenWidth) + paddingOffset;
                }
            }

            this.showHoverWindow = true;
            this.hoverCellPoint.x = x;
            this.hoverCellPoint.y = y;
        },
        onMouseLeaveCell() {
            this.showHoverWindow = false;
        },
        onClickCell(x, y, event: MouseEvent) {
            if (this.showPlanWindow &&
                this.$store.state.selectedPoint.x === x &&
                this.$store.state.selectedPoint.y === y
            ) {
                this.showPlanWindow = false;
                return;
            }
            this.$store.state.selectedPoint.x = x;
            this.$store.state.selectedPoint.y = y;
            this.showPlanWindow = true;

            this.planWindowLeftSide = (x < 7);

            const offset = 15;
            this.planWindowY = event.pageY + offset;
            if(this.isMobile) {
                this.planWindowX = this.planWindowLeftSide ? event.pageX + offset : (document.documentElement.clientWidth - event.pageX - offset);
            }
            else {
                this.planWindowX = event.pageX + offset;
            }
        },
        onClickPlan(key) {
            this.$store.state.plans.splice(this.$store.state.selectedPlanNumber-1, 0, this.getSelectedPlan(key));
            this.$store.state.plans.pop();
            if (this.$store.state.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                this.$store.state.selectedPlanNumber++;
            }
            this.showPlanWindow = false;
        },
        getSelectedPlan(key): Plan {
            return {
                key: key,
                data: {
                    name: this.$store.state.planCandidate[key].name,
                    point: {
                        x: this.$store.state.selectedPoint.x,
                        y: this.$store.state.selectedPoint.y,
                    },
                    amount: this.$store.state.selectedAmount,
                    usePoint: this.$store.state.planCandidate[key].usePoint,
                    useAmount: this.$store.state.planCandidate[key].useAmount,
                    useTargetIsland: this.$store.state.planCandidate[key].useTargetIsland,
                    targetIsland: this.$store.state.selectedTargetIsland,
                    isFiring: this.$store.state.planCandidate[key].isFiring,
                    priceString: ''
                }
            };
        },
        onClickClosePlan() {
            this.showPlanWindow = false;
        },
        onWindowSizeChanged() {
            const newScreenWidth = document.documentElement.clientWidth;
            if(this.screenWidth != newScreenWidth) {
                this.screenWidth = newScreenWidth;
                this.showHoverWindow = false;
                console.log("windowSizeChanged");
                this.showPlanWindow = false;
                this.isMobile = (document.documentElement.clientWidth < 1024);
            }
        }
    },
        computed: {
            isSelectedCell() {
                return (x, y) => {
                    if (this.$store.state.selectedPoint === null) {
                        return false;
                    }
                    return x === this.$store.state.selectedPoint.x && y === this.$store.state.selectedPoint.y
                }
            },
            isReferencedCell() {
                return (x, y) => {
                    let referencedPlan = this.$store.state.plans[this.$store.state.selectedPlanNumber-1]
                    if (!referencedPlan.usePoint) {
                        return false;
                    }
                    return x === referencedPlan.data.point.x && y === referencedPlan.data.point.y && referencedPlan.data.usePoint
                }
            },
        },
    props: [],
};
</script>

<style lang="postcss" scoped>

#island {
    margin: 0 auto;
    @apply w-full md:min-w-[496px] max-w-[496px] mb-4;

    .row {
        @apply m-0 p-0 bg-black;
        display: grid;

        .cell {
            @apply w-full aspect-square;
        }

        &:nth-child(odd) {
            grid-template-columns: 1fr repeat(15, 2fr);
        }

        &:nth-child(even) {
            grid-template-columns: repeat(15, 2fr) 1fr;
        }

        .cell-is-selected {
            border: 1px solid white;
        }

        .cell-is-referenced {
            border: 1px solid red;
        }

        .left-padding {
            @apply w-full aspect-[1/2] z-10;
            background-image: url("/img/hakoniwa/hakogif/land0.gif");
            background-position: left;
        }

        .right-padding {
            @apply relative w-full aspect-[1/2] z-10;
            background-image: url("/img/hakoniwa/hakogif/land0.gif");
            background-position: right;

            .right-padding-text {
                @apply max-xs:hidden absolute left-1 w-full leading-none text-white text-xs md:text-sm overflow-hidden z-10
            }
        }
    }

    .hover-window {
        @apply block absolute min-w-[200px] max-w-[200px] bg-black bg-opacity-50 p-1 text-white rounded-md border border-black -translate-x-1/2 z-30;

        .hover-window-header {
            @apply flex px-3 items-center;

            .hover-window-img {
                width:32px;
                height:32px;
                margin-right: 10px;
            }

            .hover-window-info {
                white-space: pre-line;
            }
        }

        .hover-window-plan {
            @apply text-sm text-left m-0 p-0;
        }

        .hover-window-plan:nth-child(2) {
            @apply border-t mt-3 pt-2 border-opacity-70 border-gray-500 ;
        }
    }

    .plan-window {
        @apply block absolute bg-gray-300 w-fit lg:max-w-[240px] rounded-md drop-shadow-md text-left overflow-hidden max-md:text-sm border border-gray-800 z-30;
        @apply animate-fadein;

        .plan-window-header {
            @apply flex p-0 m-0 bg-gray-700 text-white items-center;
        }

        .plan-window-select {
            @apply w-full px-2 max-md:py-1 hover:bg-primary;

            .action-name {
                @apply inline-block font-bold text-sm md:text-base mr-1;
            }

            .action-price {
                @apply inline-block text-gray-700 text-xs md:text-sm;
            }

            &:not(:last-child) {
                @apply border-b border-gray-700;
            }
        }
    }
}

</style>
