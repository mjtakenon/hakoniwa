<template>
  <div
    v-show="props.showHoverWindow"
    class="hover-window"
    :style="{ bottom: props.hoverWindow.y + 'px', left: props.hoverWindow.x + 'px' }">
    <div class="hover-window-header">
      <Suspense>
        <HoverCanvas class="hover-window-img" />
      </Suspense>
      <div class="hover-window-info grow items-center">
        {{ getIslandTerrain(props.hoverCellPoint.x, props.hoverCellPoint.y).data.info }}
      </div>
    </div>
    <slot></slot>
  </div>
</template>

<script setup lang="ts">
import HoverCanvas from './IslandHoverCanvas.vue'
import { Terrain } from '$js/entity//Terrain'
import { Point } from '$js/entity//Point.js'

interface Props {
  showHoverWindow: boolean
  hoverWindow: Point
  hoverCellPoint: Point
  terrains: Terrain[]
}

const props = defineProps<Props>()

const getIslandTerrain = (x, y): Terrain => {
  return props.terrains
    .filter(function (item) {
      if (item.data.point.x === x && item.data.point.y === y) return true
    })
    .pop()
}
</script>

<style lang="scss" scoped>
.hover-window {
  @apply absolute z-30 block min-w-[200px] max-w-[200px] -translate-x-1/2 rounded-md border border-black bg-black bg-opacity-50 p-1 text-white;

  .hover-window-header {
    @apply flex items-center px-3;

    .hover-window-img {
      width: 32px;
      height: 32px;
      margin-right: 10px;
    }

    .hover-window-info {
      white-space: pre-line;
    }
  }
}
</style>
