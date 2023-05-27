<template>
    <div id="island">
        <div
            class="row"
            v-for="y of store.hakoniwa.height"
            :key="y"
        >
            <div class="right-padding" :class="{'opacity-80': this.showPlanWindow}" v-if="y%2 === 1">
                <span class="right-padding-text">{{ y-1 }}</span>
            </div>
            <div class="cell" v-for="x of store.hakoniwa.width" :key="x">
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
        <countdown-widget></countdown-widget>

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
            <template v-for="(plan, index) of store.plans">
                <div class="hover-window-plan" v-if="
                    plan.data.usePoint &&
                    plan.data.point.x === hoverCellPoint.x &&
                    plan.data.point.y === hoverCellPoint.y &&
                    (!plan.data.useTargetIsland || plan.data.useTargetIsland && plan.data.targetIsland === store.island.id)
                ">
                    <span>[{{ index + 1 }}] </span>
                    <span>{{ plan.data.name }}</span>
                    <span v-if="plan.data.useAmount">
                        <span v-if="plan.data.amount === 0"> {{ plan.data.defaultAmountString }}</span>
                        <span v-else> {{ plan.data.amountString.replace(':amount:', plan.data.amount.toString()) }} </span>
                    </span>
                </div>
            </template>
        </div>
        <div v-show="showPlanWindow" class="plan-window"
             :style="[
                 { top: planWindowY + 'px'}, { left: planWindowX + 'px'}
             ]"
        >
            <div class="plan-window-header">
                <div class="grow px-3">
                    <span class="mr-2">({{store.selectedPoint.x}},{{store.selectedPoint.y}})</span>
                    <span class="text-xs">計画番号: </span>
                    <span class="mr-1">{{store.selectedPlanNumber}}</span>
                </div>

                <button
                    class="plan-window-close"
                    @click="onClickClosePlan"
                >×</button>
            </div>
            <div
                v-for="plan of this.store.planCandidate.filter(p => p.data.usePoint)"
                :key="plan.key"
                class="plan-window-select"
            >
                <div @click="onClickPlan(plan.key)">
                    <a class="action-name">{{ plan.data.name }}</a>
                    <span class="action-price">{{ plan.data.priceString }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {Terrain} from "../store/Entity/Terrain";
import {Plan} from "../store/Entity/Plan";
import {defineComponent} from "vue";
import {useMainStore} from "../store/MainStore";
import CountdownWidget from "./CountdownWidget.vue";

export default defineComponent({
    components: {
        CountdownWidget
    },
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
            isMobile: (document.documentElement.clientWidth < 1024),
        }
    },
    setup() {
        const store = useMainStore();
        return { store };
    },
    mounted() {
        window.addEventListener("resize", this.onWindowSizeChanged);
    },
    unmounted() {
        window.removeEventListener("resize", this.onWindowSizeChanged);
    },
    methods: {
        getIslandTerrain(x, y): Terrain {
            return this.store.terrains.filter(function (item, idx) {
                if (item.data.point.x === x && item.data.point.y === y) return true;
            }).pop();
        },
        onMouseOverCell(x, y, event: MouseEvent) {
            const offsetY = 25;
            this.hoverWindowY = document.documentElement.clientHeight - event.pageY + offsetY;
            this.hoverWindowX = event.pageX;

            // Screen Overflow Check
            if(this.isMobile) {
                const elementWidth = 200;
                const paddingOffset = 20;
                const leftEdge = this.hoverWindowX - (elementWidth/2);
                const rightEdge = this.hoverWindowX + (elementWidth/2);
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
                this.store.selectedPoint.x === x &&
                this.store.selectedPoint.y === y
            ) {
                this.showPlanWindow = false;
                return;
            }
            this.store.selectedPoint.x = x;
            this.store.selectedPoint.y = y;
            this.showPlanWindow = true;

            if(this.isMobile) {
                this.planWindowX = event.pageX;
                const offsetX = 15;
                const offsetY = 30;
                const elementWidth = 230;
                const leftEdge = this.planWindowX - (elementWidth/2);
                const rightEdge = this.planWindowX + (elementWidth/2);
                if (leftEdge < offsetX) {
                    this.planWindowX += (-leftEdge) + offsetX;
                }
                else if (rightEdge > this.screenWidth) {
                    this.planWindowX -= (rightEdge-this.screenWidth) + offsetX;
                }
                this.planWindowY = event.pageY + offsetY;
            }
            else {
                const offset = 15;
                this.planWindowX = event.pageX + offset;
                this.planWindowY = event.pageY + offset;
            }
        },
        onClickPlan(key) {
            this.store.plans.splice(this.store.selectedPlanNumber-1, 0, this.getSelectedPlan(key));
            this.store.plans.pop();
            if (this.store.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                this.store.selectedPlanNumber++;
            }
            this.showPlanWindow = false;
        },
        getSelectedPlan(key): Plan {
            const result = this.store.planCandidate.find(c => c.key === key);
            if (result === undefined) return null;
            else {
                const p = result.data;
                return {
                    key: key,
                    data: {
                        name: p.name,
                        point: {
                            x: this.store.selectedPoint.x,
                            y: this.store.selectedPoint.y
                        },
                        amount: this.store.selectedAmount,
                        usePoint: p.usePoint,
                        useAmount: p.useAmount,
                        useTargetIsland: p.useTargetIsland,
                        targetIsland: this.store.selectedTargetIsland,
                        isFiring: p.isFiring,
                        priceString: p.priceString,
                        amountString: p.amountString,
                        defaultAmountString: p.defaultAmountString
                    }
                }
            }
        },
        onClickClosePlan() {
            this.showPlanWindow = false;
        },
        onWindowSizeChanged() {
            const newScreenWidth = document.documentElement.clientWidth;
            if (this.screenWidth !== newScreenWidth) {
                this.screenWidth = newScreenWidth;
                this.showHoverWindow = false;
                this.showPlanWindow = false;
                this.isMobile = (document.documentElement.clientWidth < 1024);
            }
        },
    },
    computed: {
        isSelectedCell() {
            return (x, y) => {
                if (this.store.selectedPoint === null) {
                    return false;
                }
                return x === this.store.selectedPoint.x && y === this.store.selectedPoint.y
            }
        },
        isReferencedCell() {
            return (x, y) => {
                let referencedPlan = this.store.plans[this.store.selectedPlanNumber-1]
                if (!referencedPlan.data.usePoint) {
                    return false;
                }
                if (referencedPlan.data.useTargetIsland && referencedPlan.data.targetIsland !== this.store.island.id) {
                    return false;
                }
                return x === referencedPlan.data.point.x && y === referencedPlan.data.point.y && referencedPlan.data.usePoint
            }
        },
    },
    props: [],
});
</script>

<style lang="postcss" scoped>

#island {
    margin: 0 auto;
    @apply w-full md:min-w-[496px] max-w-[496px];

    .row {
        @apply m-0 -mt-[0.1px] p-0 bg-black;
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
                @apply max-xs:hidden absolute left-1 leading-none text-white text-xs md:text-sm overflow-hidden z-10
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
        @apply block absolute bg-surface-variant text-on-surface-variant w-fit max-lg:min-w-[230px] max-lg:max-w-[230px] max-lg:-translate-x-1/2 lg:max-w-[240px] rounded-md drop-shadow-xl text-left overflow-hidden max-md:text-sm border border-primary dark:border-primary-container z-30;
        @apply animate-fadein;

        .plan-window-header {
            @apply flex p-0 m-0 bg-primary dark:bg-primary-container text-on-primary dark:text-on-primary-container items-center;

            .plan-window-close {
                @apply inline-block bg-primary dark:bg-primary-container text-on-primary dark:text-on-primary-container p-0 border-none hover:bg-primary hover:dark:bg-primary-container drop-shadow-none mr-3;
            }
        }

        .plan-window-select {
            @apply w-full px-2 max-md:py-1 hover:bg-on-primary;

            .action-name {
                @apply inline-block font-bold text-sm md:text-base mr-1;
            }

            .action-price {
                @apply inline-block text-xs;
            }

            &:not(:last-child) {
                @apply border-b border-gray-700;
            }
        }
    }
}

</style>
