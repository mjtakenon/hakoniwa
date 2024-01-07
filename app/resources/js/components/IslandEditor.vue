<template>
    <div id="island">
        <TresCanvas v-bind="gl" :class="['island-canvas', {'opacity-80': store.showPlanWindow}]">
            <TresPerspectiveCamera
                :position="[64, 192, 192] as Vector3"
            />
            <CameraControls
                v-bind="cameraControlsState"
                make-default
            />

            <Suspense>
                <IslandEditorCanvas :terrains="store.terrains"/>
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
        <PlanWindow/>
    </div>
</template>

<script setup lang="ts">
import {onBeforeMount, onMounted, onUnmounted, reactive} from "vue";
import {useMainStore} from "../store/MainStore";
import CountdownWidget from "./CountdownWidget.vue";
import {TresCanvas} from "@tresjs/core";
import {BasicShadowMap, NoToneMapping, SRGBColorSpace, Vector3} from "three";
import IslandEditorCanvas from "./IslandEditorCanvas.vue";
import {CameraControls} from "@tresjs/cientos";
import HoverWindow from "./HoverWindow.vue";
import PlanWindow from "./PlanWindow.vue";

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

let screenWidth = document.documentElement.clientWidth
let terrains = []

onBeforeMount(() => {
    store.isIslandEditorMount = true
    terrains = new Array(store.hakoniwa.height);
    for (let y = 0; y < terrains.length; y++) {
        terrains[y] = new Array(store.hakoniwa.width);
    }

    for (let terrain of store.terrains) {
        terrains[terrain.data.point.y][terrain.data.point.x] = terrain;
    }

    store.targetTerrains[store.island.id] = store.terrains
})

onMounted(() => {
    window.addEventListener("resize", onWindowSizeChanged)

    store.screenWidth = document.documentElement.clientWidth
    store.isMobile = (document.documentElement.clientWidth < 1024)
})

onUnmounted(() => {
    store.isIslandEditorMount = false
    window.removeEventListener("resize", onWindowSizeChanged)
})

const onWindowSizeChanged = () => {
    const newScreenWidth = document.documentElement.clientWidth;
    if (screenWidth !== newScreenWidth) {
        store.screenWidth = newScreenWidth;
        store.isMobile = (document.documentElement.clientWidth < 1024);
    }
}

</script>

<style lang="scss" scoped>

.island-canvas {
    @apply w-full min-h-[496px] max-h-[496px];
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

    .hover-window-plan {
        @apply text-sm text-left m-0 p-0;
    }

    .hover-window-plan:nth-child(2) {
        @apply border-t mt-3 pt-2 border-opacity-70 border-gray-500 ;
    }
}

</style>
