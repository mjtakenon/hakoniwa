<template>
  <div id="island">
    <TresCanvas v-bind="gl" class="island-canvas">
      <TresPerspectiveCamera :position="[64, 192, 192] as Vector3" />
      <CameraControls v-bind="cameraControlsState" make-default />

      <Suspense>
        <IslandViewerCanvas />
      </Suspense>

      <TresAmbientLight :intensity="2" />
      <TresDirectionalLight :position="[192, 192, 192] as Vector3" :intensity="3" />
    </TresCanvas>
    <IslandHoverWindow
      :showHoverWindow="store.showHoverWindow"
      :hoverWindowY="store.hoverWindowY"
      :hoverWindowX="store.hoverWindowX"
      :hoverCellPoint="store.hoverCellPoint"
      :terrains="store.terrains" />
  </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'
import { TresCanvas } from '@tresjs/core'
import { BasicShadowMap, NoToneMapping, SRGBColorSpace, Vector3 } from 'three'
import { CameraControls } from '@tresjs/cientos'
import IslandHoverWindow from '../../islands/Hover/IslandHoverWindow.vue'
import IslandViewerCanvas from '../../islands/Viewer/IslandViewerCanvas.vue'
import { useIslandViewerStore } from '../../../store/IslandViewerStore.js'

const gl = reactive({
  clearColor: '#888888',
  shadows: true,
  alpha: true,
  shadowMapType: BasicShadowMap,
  outputColorSpace: SRGBColorSpace,
  toneMapping: NoToneMapping
})

const cameraControlsState = reactive({
  minDistance: 20,
  maxDistance: 200,
  maxPolarAngle: Math.PI / 2
})

const store = useIslandViewerStore()
</script>

<style lang="scss" scoped>
.island-canvas {
  @apply mb-4 max-h-[512px] min-h-[512px] w-full;
}

#island {
  margin: 0 auto;
  @apply mb-4 w-full max-w-[512px] md:min-w-[512px];

  .row {
    @apply m-0 bg-black p-0;
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
}
</style>
