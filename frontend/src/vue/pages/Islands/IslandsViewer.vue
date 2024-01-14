<template>
  <TresPerspectiveCamera :position="[8, 200, 32] as Vector3" :look-at="[0, 0, 0]" />
  <slot />
  <Suspense>
    <IslandViewerCanvas />
  </Suspense>

  <TresAmbientLight :intensity="2" />
  <TresDirectionalLight :position="[192, 192, 192] as Vector3" :intensity="3" />
</template>

<script setup lang="ts">
import { Vector3 } from 'three'
import IslandViewerCanvas from '$vue/components/islands/Viewer/IslandViewerCanvas.vue'
</script>

<style lang="scss" scoped></style>
