<template>
    <div id="island">
        <TresCanvas v-bind="gl" :class="['island-canvas', {'opacity-80': showPlanWindow}]">
            <TresPerspectiveCamera
                :position="[64, 192, 192] as Vector3"
            />
            <CameraControls
                v-bind="cameraControlsState"
                make-default
            />

            <Suspense>
                <!--                以下未実装 -->
                <!--                isSelectedCell(x-1, y-1) && this.showPlanWindow ? 'cell-is-selected' : '
                !isSelectedCell(x-1, y-1) && this.showPlanWindow ? 'opacity-80' : '',
                isReferencedCell(x-1, y-1) ? 'cell-is-referenced' : '',-->
                <IslandCanvas/>
            </Suspense>

            <TresAmbientLight :intensity="2"/>
            <TresDirectionalLight
                :position="[192, 192, 192] as Vector3"
                :intensity="3"
            />
        </TresCanvas>
        <countdown-widget></countdown-widget>
        <HoverWindow>
            <template v-for="(plan, index) of store.plans">
                <div class="hover-window-plan" v-if="
                    plan.data.usePoint &&
                    plan.data.point.x === store.hoverCellPoint.x &&
                    plan.data.point.y === store.hoverCellPoint.y &&
                    (!plan.data.useTargetIsland || plan.data.useTargetIsland && plan.data.targetIsland === store.island.id)
                ">
                    <span>[{{ index + 1 }}] </span>
                    <span>{{ plan.data.name }}</span>
                    <span v-if="plan.data.useAmount">
                        <span v-if="plan.data.amount === 0"> {{ plan.data.defaultAmountString }}</span>
                        <span v-else> {{
                                plan.data.amountString.replace(':amount:', plan.data.amount.toString())
                            }} </span>
                    </span>
                </div>
            </template>
        </HoverWindow>

        <div v-show="showPlanWindow" class="plan-window"
             :style="[
                 { top: planWindowY + 'px'}, { left: planWindowX + 'px'}
             ]"
        >
            <div class="plan-window-header">
                <div class="grow px-3">
                    <span class="mr-2">({{ store.selectedPoint.x }},{{ store.selectedPoint.y }})</span>
                    <span class="text-xs">計画番号: </span>
                    <span class="mr-1">{{ store.selectedPlanNumber }}</span>
                </div>

                <button
                    class="plan-window-close"
                    @click="onClickClosePlan"
                >×
                </button>
            </div>
            <div
                v-for="plan of store.planCandidate.filter(p => p.data.usePoint)"
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

<script setup lang="ts">
import {computed, onBeforeMount, reactive} from "vue";
import {Terrain} from "../store/Entity/Terrain";
import {Plan} from "../store/Entity/Plan";
import {useMainStore} from "../store/MainStore";
import CountdownWidget from "./CountdownWidget.vue";
import {TresCanvas} from "@tresjs/core";
import {BasicShadowMap, NoToneMapping, SRGBColorSpace, Vector3} from "three";
import IslandCanvas from "./IslandCanvas.vue";
import {CameraControls} from "@tresjs/cientos";
import HoverWindow from "./HoverWindow.vue";

const gl = reactive({
    clearColor: '#888888',
    shadows: true,
    alpha: true,
    shadowMapType: BasicShadowMap,
    outputColorSpace: SRGBColorSpace,
    toneMapping: NoToneMapping,
})

const cameraControlsState = reactive({
    minDistance: 20,
    maxDistance: 200,
    maxPolarAngle: Math.PI / 2,
})

const store = useMainStore()

const MAX_PLAN_NUMBER = 30
let showPlanWindow = false
let screenWidth = document.documentElement.clientWidth
let planWindowY = 0
let planWindowX = 0
let isMobile = (document.documentElement.clientWidth < 1024)
let terrains = []

const getIslandTerrain = (x, y): Terrain => {
    return store.terrains.filter(function (item) {
        if (item.data.point.x === x && item.data.point.y === y) return true;
    }).pop()
}

const onMouseOverCell = (x, y, event: MouseEvent) => {
    const offsetY = 25;
    store.hoverWindowY = document.documentElement.clientHeight - event.pageY + offsetY;
    store.hoverWindowX = event.pageX;

    // Screen Overflow Check
    if (isMobile) {
        const elementWidth = 200;
        const paddingOffset = 20;
        const leftEdge = store.hoverWindowX - (elementWidth / 2);
        const rightEdge = store.hoverWindowX + (elementWidth / 2);
        if (leftEdge < paddingOffset) {
            store.hoverWindowX += (-leftEdge) + paddingOffset;
        } else if (rightEdge > screenWidth) {
            store.hoverWindowX -= (rightEdge - screenWidth) + paddingOffset;
        }
    }

    store.showHoverWindow = true;
    store.hoverCellPoint.x = x;
    store.hoverCellPoint.y = y;
}

const onMouseLeaveCell = () => {
    store.showHoverWindow = false;
}

const onClickCell = (x, y, event: MouseEvent) => {
    if (showPlanWindow &&
        store.selectedPoint.x === x &&
        store.selectedPoint.y === y
    ) {
        showPlanWindow = false;
        return;
    }
    store.selectedPoint.x = x;
    store.selectedPoint.y = y;
    showPlanWindow = true;

    if (isMobile) {
        planWindowX = event.pageX;
        const offsetX = 15;
        const offsetY = 30;
        const elementWidth = 230;
        const leftEdge = planWindowX - (elementWidth / 2);
        const rightEdge = planWindowX + (elementWidth / 2);
        if (leftEdge < offsetX) {
            planWindowX += (-leftEdge) + offsetX;
        } else if (rightEdge > screenWidth) {
            planWindowX -= (rightEdge - screenWidth) + offsetX;
        }
        planWindowY = event.pageY + offsetY;
    } else {
        const offset = 15;
        planWindowX = event.pageX + offset;
        planWindowY = event.pageY + offset;
    }
}

const onClickPlan = (key) => {
    store.plans.splice(store.selectedPlanNumber - 1, 0, getSelectedPlan(key));
    store.plans.pop();
    if (store.selectedPlanNumber < MAX_PLAN_NUMBER) {
        store.selectedPlanNumber++;
    }
    showPlanWindow = false;
}

const getSelectedPlan = (key): Plan => {
    const result = store.planCandidate.find(c => c.key === key);
    if (result === undefined) return null;
    else {
        const p = result.data;
        return {
            key: key,
            data: {
                name: p.name,
                point: {
                    x: store.selectedPoint.x,
                    y: store.selectedPoint.y
                },
                amount: store.selectedAmount,
                usePoint: p.usePoint,
                useAmount: p.useAmount,
                useTargetIsland: p.useTargetIsland,
                targetIsland: store.selectedTargetIsland,
                isFiring: p.isFiring,
                priceString: p.priceString,
                amountString: p.amountString,
                defaultAmountString: p.defaultAmountString
            }
        }
    }
}

const onClickClosePlan = () => {
    showPlanWindow = false;
}

const onWindowSizeChanged = () => {
    const newScreenWidth = document.documentElement.clientWidth;
    if (screenWidth !== newScreenWidth) {
        screenWidth = newScreenWidth;
        store.showHoverWindow = false;
        showPlanWindow = false;
        isMobile = (document.documentElement.clientWidth < 1024);
    }
}

const isSelectedCell = computed((x, y) => {
    if (store.selectedPoint === null) {
        return false;
    }
    return x === store.selectedPoint.x && y === store.selectedPoint.y
})

const isReferencedCell = computed((x, y) => {
    let referencedPlan = store.plans[store.selectedPlanNumber - 1]
    if (!referencedPlan.data.usePoint) {
        return false;
    }
    if (referencedPlan.data.useTargetIsland && referencedPlan.data.targetIsland !== store.island.id) {
        return false;
    }
    return x === referencedPlan.data.point.x && y === referencedPlan.data.point.y && referencedPlan.data.usePoint
})

onBeforeMount(() => {
    terrains = new Array(store.hakoniwa.height);
    for (let y = 0; y < terrains.length; y++) {
        terrains[y] = new Array(store.hakoniwa.width);
    }

    for (let terrain of store.terrains) {
        terrains[terrain.data.point.y][terrain.data.point.x] = terrain;
    }
})

</script>

<style lang="scss" scoped>

.island-canvas {
    @apply w-full min-h-[496px] min-h-[496px] max-h-[496px] max-h-[496px];
}

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
