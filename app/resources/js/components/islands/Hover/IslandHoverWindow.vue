<template>
  <div
    v-show="store.showHoverWindow"
    class="hover-window"
    :style="{ bottom: store.hoverWindowY + 'px', left: store.hoverWindowX + 'px' }">
    <div class="hover-window-header">
      <Suspense>
        <HoverCanvas class="hover-window-img" />
      </Suspense>
      <div class="hover-window-info grow items-center">
        {{ getIslandTerrain(store.hoverCellPoint.x, store.hoverCellPoint.y).data.info }}
      </div>
    </div>
    <slot></slot>
  </div>
</template>

<script setup lang="ts">
import { useMainStore } from '../../../store/MainStore'
import HoverCanvas from './IslandHoverCanvas.vue'
import { Terrain } from '../../../store/Entity/Terrain'

const store = useMainStore()

const getIslandTerrain = (x, y): Terrain => {
  return store.terrains
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
