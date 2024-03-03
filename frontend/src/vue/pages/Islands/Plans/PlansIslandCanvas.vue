<template>
  <TresCanvas v-bind="gl" :class="['island-canvas', { 'opacity-80': islandEditorStore.showPlanWindow }]">
    <PlansIslandEditor v-if="!islandEditorStore.isIslandPopupMount && !islandEditorStore.isOpenPopup" />
  </TresCanvas>
</template>

<script setup lang="ts">
import PlansIslandEditor from './PlansIslandEditor.vue'
import { onBeforeMount, onMounted, onUnmounted, reactive } from 'vue'
import { useIslandEditorStore } from '$store/IslandEditorStore.js'
import { TresCanvas } from '@tresjs/core'
import { NoToneMapping, SRGBColorSpace, VSMShadowMap } from 'three'
import { useIslandViewerStore } from '$store/IslandViewerStore.js'

const gl = reactive({
  clearColor: '#888888',
  shadows: true,
  alpha: true,
  shadowMapType: VSMShadowMap,
  outputColorSpace: SRGBColorSpace,
  toneMapping: NoToneMapping
})

const islandEditorStore = useIslandEditorStore()
const islandViewerStore = useIslandViewerStore()

onBeforeMount(() => {
  islandEditorStore.isIslandEditorMount = true
  islandEditorStore.targetTerrains[islandViewerStore.island.id] = islandViewerStore.terrain
})

onMounted(() => {
  window.addEventListener('resize', onWindowSizeChanged)
})

onUnmounted(() => {
  islandEditorStore.isIslandEditorMount = false
  window.removeEventListener('resize', onWindowSizeChanged)
})

const onWindowSizeChanged = () => {
  const newScreenWidth = document.documentElement.clientWidth
  if (islandViewerStore.screenWidth !== newScreenWidth) {
    islandViewerStore.screenWidth = newScreenWidth
    islandViewerStore.isMobile = document.documentElement.clientWidth < 1024
  }
}
</script>

<style lang="scss" scoped>
#plan-page {
  @apply mx-auto min-h-[1200px] max-w-[1000px] text-center;

  .island-editor-padding {
    margin: 0 auto;
    @apply w-full max-w-[496px] md:min-w-[496px];
  }
}

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
