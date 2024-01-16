<template>
  <TresPerspectiveCamera :position="[8, 200, 32] as Vector3" :look-at="[0, 0, 0]" />
  <CameraControls />
  <Suspense>
    <IslandViewerTerrain />
  </Suspense>

  <TresAmbientLight :intensity="0.5" />
  <TresDirectionalLight :position="[10, 200, 100] as Vector3" cast-shadow :intensity="8" v-bind="{ color: 0xffdddd }" />
</template>

<script setup lang="ts">
import { Vector3 } from 'three'
import IslandViewerTerrain from '$vue/components/islands/Viewer/IslandViewerTerrain.vue'
import { useTresContext } from '@tresjs/core'
import { onMounted } from 'vue'
import CameraControls from '$vue/components/islands/Camera/CameraControls.vue'

const context = useTresContext()

onMounted(() => {
  context.renderer.value.shadowMap.enabled = true
  context.scene.value.children[2].shadow.camera.right = 100
  context.scene.value.children[2].shadow.camera.left = -100
  context.scene.value.children[2].shadow.camera.top = -100
  context.scene.value.children[2].shadow.camera.bottom = 100
  context.scene.value.children[2].shadow.mapSize.width = 2048
  context.scene.value.children[2].shadow.mapSize.height = 2048
  context.scene.value.children[2].shadow.radius = 0.5
})

// useRenderLoop().onLoop(({ delta, elapsed }) => {
//   context.scene.value.children[2].position.x = Math.sin(elapsed / 10) * 100
//   context.scene.value.children[2].position.y = Math.cos(elapsed / 10) * 100
// })
</script>

<style lang="scss" scoped></style>
