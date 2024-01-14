<template>
  <div id="island">
    <TresCanvas v-bind="gl" :class="['island-canvas', { 'opacity-80': islandEditorStore.showPlanWindow }]">
      <TresPerspectiveCamera :position="[8, 200, 32] as Vector3" />

      <slot />
      <Suspense>
        <IslandEditorCanvas :terrains="islandViewerStore.terrains" />
      </Suspense>

      <TresAmbientLight :intensity="2" />
      <TresDirectionalLight :position="[192, 192, 192] as Vector3" :intensity="3" />
    </TresCanvas>
    <CountdownWidget></CountdownWidget>
    <IslandHoverWindow
      :showHoverWindow="islandViewerStore.showHoverWindow"
      :hoverWindow="islandViewerStore.hoverWindow"
      :hoverCellPoint="islandViewerStore.hoverCellPoint"
      :terrains="islandViewerStore.terrains">
      <template v-for="(plan, index) of islandEditorStore.plans">
        <div
          class="hover-window-plan"
          v-if="
            plan.data.usePoint &&
            plan.data.point.x === islandViewerStore.hoverCellPoint.x &&
            plan.data.point.y === islandViewerStore.hoverCellPoint.y &&
            (!plan.data.useTargetIsland ||
              (plan.data.useTargetIsland && plan.data.targetIsland === islandViewerStore.island.id))
          ">
          <span>[{{ index + 1 }}] </span>
          <span>{{ plan.data.name }}</span>
          <span v-if="plan.data.useAmount">
            <span v-if="plan.data.amount === 0"> {{ plan.data.defaultAmountString }}</span>
            <span v-else> {{ plan.data.amountString.replace(':amount:', plan.data.amount.toString()) }} </span>
          </span>
        </div>
      </template>
    </IslandHoverWindow>
    <PlanWindow />
  </div>
</template>

<script setup lang="ts">
import { onBeforeMount, onMounted, onUnmounted, reactive } from 'vue'
import CountdownWidget from '$vue/components/islands/common/CountdownWidget.vue'
import { TresCanvas } from '@tresjs/core'
import { BasicShadowMap, NoToneMapping, SRGBColorSpace, Vector3 } from 'three'
import IslandEditorCanvas from '$vue/components/islands/Editor/IslandEditorCanvas.vue'
import PlanWindow from '$vue/components/islands/Editor/IslandEditorPlanWindow.vue'
import IslandHoverWindow from '$vue/components/islands/Hover/IslandHoverWindow.vue'
import { useIslandEditorStore } from '$store/IslandEditorStore.js'
import { useIslandViewerStore } from '$store/IslandViewerStore.js'

const gl = reactive({
  clearColor: '#888888',
  shadows: true,
  alpha: true,
  shadowMapType: BasicShadowMap,
  outputColorSpace: SRGBColorSpace,
  toneMapping: NoToneMapping
})

const islandEditorStore = useIslandEditorStore()
const islandViewerStore = useIslandViewerStore()

let screenWidth = document.documentElement.clientWidth
let terrains = []

onBeforeMount(() => {
  islandEditorStore.isIslandEditorMount = true
  terrains = new Array(islandViewerStore.hakoniwa.height)
  for (let y = 0; y < terrains.length; y++) {
    terrains[y] = new Array(islandViewerStore.hakoniwa.width)
  }

  for (let terrain of islandViewerStore.terrains) {
    terrains[terrain.data.point.y][terrain.data.point.x] = terrain
  }

  islandEditorStore.targetTerrains[islandViewerStore.island.id] = islandViewerStore.terrains
})

onMounted(() => {
  window.addEventListener('resize', onWindowSizeChanged)

  islandViewerStore.screenWidth = document.documentElement.clientWidth
  islandViewerStore.isMobile = document.documentElement.clientWidth < 1024
})

onUnmounted(() => {
  islandEditorStore.isIslandEditorMount = false
  window.removeEventListener('resize', onWindowSizeChanged)
})

const onWindowSizeChanged = () => {
  const newScreenWidth = document.documentElement.clientWidth
  if (screenWidth !== newScreenWidth) {
    islandViewerStore.screenWidth = newScreenWidth
    islandViewerStore.isMobile = document.documentElement.clientWidth < 1024
  }
}
</script>

<style lang="scss" scoped>
.island-canvas {
  @apply max-h-[496px] min-h-[496px] w-full;
}

#island {
  margin: 0 auto;
  @apply w-full max-w-[496px] md:min-w-[496px];

  .row {
    @apply m-0 -mt-[0.1px] bg-black p-0;
    display: grid;

    .cell {
      @apply aspect-square w-full;
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
      @apply z-10 aspect-[1/2] w-full;
      background-image: url('/img/hakoniwa/hakogif/land0.gif');
      background-position: left;
    }

    .right-padding {
      @apply relative z-10 aspect-[1/2] w-full;
      background-image: url('/img/hakoniwa/hakogif/land0.gif');
      background-position: right;

      .right-padding-text {
        @apply absolute left-1 z-10 overflow-hidden text-xs leading-none text-white max-xs:hidden md:text-sm;
      }
    }
  }

  .hover-window-plan {
    @apply m-0 p-0 text-left text-sm;
  }

  .hover-window-plan:nth-child(2) {
    @apply mt-3 border-t border-gray-500 border-opacity-70 pt-2;
  }
}
</style>
