<template>
  <TresPerspectiveCamera :position="[8, 200, 32] as Vector3" :look-at="[0, 0, 0]" />
  <CameraControls />
  <Suspense>
    <IslandEditorTerrain :terrain="islandViewerStore.terrain" />
  </Suspense>

  <TresAmbientLight :intensity="0.5" />
  <TresDirectionalLight :position="[10, 200, 100] as Vector3" cast-shadow :intensity="8" v-bind="{ color: 0xffdddd }" />
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { Vector3 } from 'three'
import IslandEditorTerrain from '$vue/components/islands/Editor/IslandEditorTerrain.vue'
import { useIslandViewerStore } from '$store/IslandViewerStore.js'
import CameraControls from '$vue/components/islands/Camera/CameraControls.vue'
import { useTresContext } from '@tresjs/core'

const islandViewerStore = useIslandViewerStore()
const context = useTresContext()

onMounted(() => {
  islandViewerStore.screenWidth = document.documentElement.clientWidth
  islandViewerStore.isMobile = document.documentElement.clientWidth < 1024

  context.renderer.value.shadowMap.enabled = true
  context.scene.value.children[2].shadow.camera.right = 100
  context.scene.value.children[2].shadow.camera.left = -100
  context.scene.value.children[2].shadow.camera.top = -100
  context.scene.value.children[2].shadow.camera.bottom = 100
  context.scene.value.children[2].shadow.mapSize.width = 1000
  context.scene.value.children[2].shadow.mapSize.height = 1000
  context.scene.value.children[2].shadow.radius = 0.5
})

// useRenderLoop().onLoop(({ delta, elapsed }) => {
//   context.scene.value.children[2].position.x = Math.sin(elapsed / 100) * 100
//   context.scene.value.children[2].position.y = Math.cos(elapsed / 100) * 100
// })
</script>

<style lang="scss" scoped></style>
