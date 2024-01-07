<template>
    <div class="popup" :class="{'active' : store.isOpenPopup}">
        <div class="popup-background" @click="closePopup"></div>
        <div class="popup-window">
            <div class="popup-window-header">
                <div class="popup-title-target">target:</div>
                <div class="popup-island-name" :class="titleStyle">
                    {{ store.selectedTargetIslandName }}島
                </div>
                <button class="close-button" @click="closePopup">
                    ×
                </button>
            </div>
            <div v-if="store.isLoadingTerrain" class="loading">
                <svg aria-hidden="true" class="loading-circle" viewBox="0 0 100 101" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor"/>
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill"/>
                </svg>
            </div>
            <TresCanvas v-else v-bind="gl" :class="['island-canvas', {'opacity-80': store.showPlanWindow}]">
                <TresPerspectiveCamera
                    :position="[64, 192, 192] as Vector3"
                />
                <CameraControls
                    v-bind="cameraControlsState"
                    make-default
                />

                <Suspense>
                    <IslandEditorCanvas v-if="store.targetTerrains[store.selectedTargetIsland] !== undefined" :terrains="store.targetTerrains[store.selectedTargetIsland]"/>
                </Suspense>

                <TresAmbientLight :intensity="2"/>
                <TresDirectionalLight
                    :position="[192, 192, 192] as Vector3"
                    :intensity="3"
                />
            </TresCanvas>
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
            <div class="comment-box">
                <div class="comment-title">
                    Comment:
                </div>
                <div class="comment-text">
                    {{ islandComment }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {computed, onBeforeMount, onMounted, onUnmounted, reactive, watch} from "vue"
import {useMainStore} from "../store/MainStore"
import {storeToRefs} from "pinia"
import {Terrain} from "../store/Entity/Terrain"
import {Point} from "../store/Entity/Point"
import {Plan} from "../store/Entity/Plan"
import {BasicShadowMap, NoToneMapping, SRGBColorSpace, Vector3} from "three";
import {TresCanvas} from "@tresjs/core";
import IslandEditorCanvas from "./IslandEditorCanvas.vue";
import {CameraControls} from "@tresjs/cientos";
import HoverWindow from "./HoverWindow.vue";
import PlanWindow from "./PlanWindow.vue";

const MAX_PLAN_NUMBER = 30
let targetTerrains: Terrain[] = []
let hoverCellPoint: Point = {x: 0, y: 0}
let hoverWindowY = 170
let hoverWindowX = 0
let screenWidth = document.documentElement.clientWidth
let planWindowY = 0
let planWindowX = 0
let isMobile = (document.documentElement.clientWidth < 1024)

const store = useMainStore()
const {isOpenPopup, isLoadingTerrain} = storeToRefs(store)

const gl = reactive({
    clearColor: '#888888',
    shadows: true,
    alpha: true,
    shadowMapType: BasicShadowMap,
    outputColorSpace: SRGBColorSpace,
    toneMapping: NoToneMapping,
    width: 100,
})

const cameraControlsState = reactive({
    minDistance: 20,
    maxDistance: 200,
    maxPolarAngle: Math.PI / 2,
})

onBeforeMount(() => {
    store.isIslandPopupMount = true
})

onMounted(() => {
    window.addEventListener("resize", onWindowSizeChanged)
})

onUnmounted(() => {
    store.isIslandPopupMount = false
    window.removeEventListener("resize", onWindowSizeChanged)
})

watch(isOpenPopup, () => {
    if (store.isOpenPopup) {
        document.addEventListener("wheel", preventScroll, {passive: false})
        document.addEventListener("touchmove", preventScroll, {passive: false})
    } else {
        document.removeEventListener("wheel", preventScroll)
        document.removeEventListener("touchmove", preventScroll)
    }
})

watch(isLoadingTerrain, () => {
    if (store.isLoadingTerrain) return
    const target = store.targetIslands.filter(island => island.id === store.selectedTargetIsland)
    if (target.length < 1) throw new Error("対象の島が見つかりません")
    if (target[0].terrains === undefined) throw new Error("目標の島に地形情報がありません")

    // 取得した目標島の地形を保存
    store.targetTerrains[store.selectedTargetIsland] = target[0].terrains
    store.targetIslandComments[store.selectedTargetIsland] = target[0].comment
})

const isSelectedCell = (x, y) => {
    if (store.selectedPoint === null) {
        return false
    }
    return x === store.selectedPoint.x && y === store.selectedPoint.y
}

const titleStyle = computed(() => {
    if (store.selectedTargetIslandName.length > 16) {
        return 'text-[0.5rem] lg:text-sm'
    }
    return 'text-base lg:text-lg'
})

const hasComment = computed(() => {
    return store.targetIslandComments[store.selectedTargetIsland] === null || store.targetIslandComments[store.selectedTargetIsland] === undefined || store.targetIslandComments[store.selectedTargetIsland] === ""
})

const islandComment = computed(() => {
    if (hasComment) {
        return "コメントはありません"
    } else {
        return store.targetIslandComments[store.selectedTargetIsland]
    }
})

const closePopup = () => {
    onMouseLeaveCell()
    onClickClosePlan()
    store.isOpenPopup = false
    store.showPlanWindow = false
}

const preventScroll = (event: MouseEvent | TouchEvent) => {
    event.preventDefault()
}

const getIslandTerrainImage = (x, y): string => {
    if (targetTerrains.length < 1) return ""
    return targetTerrains.filter(item => {
        if (item.data.point.x === x && item.data.point.y === y) return true
    }).pop().data.image_path
}

const getIslandTerrainInfo = (x, y): string => {
    if (targetTerrains.length < 1) return ""
    return targetTerrains.filter(item => {
        if (item.data.point.x === x && item.data.point.y === y) return true
    }).pop().data.info
}

const onMouseOverCell = (x, y, event: MouseEvent) => {
    const offsetY = 25
    hoverWindowY = document.documentElement.clientHeight - event.clientY + offsetY
    hoverWindowX = event.clientX

    // Screen Overflow Check
    if (isMobile) {
        const elementWidth = 200
        const paddingOffset = 20
        const leftEdge = hoverWindowX - (elementWidth / 2)
        const rightEdge = hoverWindowX + (elementWidth / 2)
        if (leftEdge < paddingOffset) {
            hoverWindowX += (-leftEdge) + paddingOffset
        } else if (rightEdge > screenWidth) {
            hoverWindowX -= (rightEdge - screenWidth) + paddingOffset
        }
    }

    store.showHoverWindow = true
    hoverCellPoint.x = x
    hoverCellPoint.y = y
}

const onClickPlan = (key) => {
    store.plans.splice(store.selectedPlanNumber - 1, 0, getSelectedPlan(key))
    store.plans.pop()
    if (store.selectedPlanNumber < MAX_PLAN_NUMBER) {
        store.selectedPlanNumber++
    }
    store.showPlanWindow = false
}
const getSelectedPlan = (key): Plan => {
    const result = store.planCandidate.find(c => c.key === key)
    if (result === undefined) return null
    else {
        const p = result.data
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
const onMouseLeaveCell = () => {
    store.showHoverWindow = false
}

const onClickCell = (x, y, event: MouseEvent) => {
    if (store.showPlanWindow &&
        store.selectedPoint.x === x &&
        store.selectedPoint.y === y
    ) {
        store.showPlanWindow = false
        return
    }
    store.selectedPoint = {x: x, y: y}
    store.showPlanWindow = true

    if (isMobile) {
        planWindowX = event.clientX
        const offsetX = 15
        const offsetY = 30
        const elementWidth = 230
        const leftEdge = planWindowX - (elementWidth / 2)
        const rightEdge = planWindowX + (elementWidth / 2)
        if (leftEdge < offsetX) {
            planWindowX += (-leftEdge) + offsetX
        } else if (rightEdge > screenWidth) {
            planWindowX -= (rightEdge - screenWidth) + offsetX
        }
        planWindowY = event.clientY + offsetY
    } else {
        const offset = 15
        planWindowX = event.clientX + offset
        planWindowY = event.clientY + offset
    }
}

const onClickClosePlan = () => {
    store.showPlanWindow = false
}

const onWindowSizeChanged = () => {
    const newScreenWidth = document.documentElement.clientWidth
    if (screenWidth != newScreenWidth) {
        screenWidth = newScreenWidth
        store.showHoverWindow = false
        store.showPlanWindow = false
        isMobile = (document.documentElement.clientWidth < 1024)
    }
}

</script>

<style scoped lang="scss">

.island-canvas {
    @apply min-h-[496px] min-w-[496px] max-h-[496px] max-w-[496px];
}

.popup {
    @apply hidden;

    &.active {
        @apply flex items-center justify-center fixed w-full h-screen top-0 left-0 z-50;
    }
}

.popup-background {
    @apply absolute w-full h-screen bg-[rgba(0,0,0,0.7)] -z-10;
}

.popup-window {
    // general
    @apply w-fit pb-2 bg-background text-on-background rounded-xl;
    // desktop
    @apply md:px-2 md:max-w-[calc(498px+1rem)];

    .popup-window-header {
        @apply w-full flex justify-between items-center px-4 py-1;

        .popup-title-target {
            // general
            @apply text-on-surface-variant;
            // sp
            @apply text-xs mr-1;
            // desktop
            @apply md:text-sm md:mr-2;
        }

        .popup-island-name {
            @apply text-left font-bold grow min-w-0 max-w-[80%] leading-none py-1;
        }

        .close-button {
            @apply ml-auto p-0 text-xl bg-background border-none hover:bg-background drop-shadow-none;
        }
    }

    .comment-box {
        @apply w-full max-w-[clamp(0px,95vw,498px)] mt-2 text-left mx-auto px-2 leading-none;

        .comment-title {
            @apply text-xs md:text-sm text-on-surface-variant;
        }

        .comment-text {
            @apply text-sm px-1 leading-none md:text-base md:leading-none text-on-surface-variant;
        }
    }

    .hover-window-plan {
        @apply text-sm text-left m-0 p-0;
    }

    .hover-window-plan:nth-child(2) {
        @apply border-t mt-3 pt-2 border-opacity-70 border-gray-500 ;
    }
}

.loading {
    // general
    @apply flex items-center justify-center;
    // sp
    @apply w-[100vw] h-[100vw];
    // desktop
    @apply max-w-[498px] max-h-[498px];

    .loading-circle {
        @apply w-1/6 h-1/6 text-surface-variant animate-spin fill-primary;
    }
}

</style>
