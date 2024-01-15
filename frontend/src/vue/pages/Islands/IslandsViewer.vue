<template>
  <TresPerspectiveCamera :position="[8, 200, 32] as Vector3" :look-at="[0, 0, 0]" />
  <slot />
  <Suspense>
    <IslandViewerCanvas />
  </Suspense>

  <TresAmbientLight :intensity="1" />
  <TresDirectionalLight
    :position="[100, 200, 100] as Vector3"
    cast-shadow
    :intensity="8"
    v-bind="{ color: 0xffdddd }" />
</template>

<script setup lang="ts">
import { Vector3, WebGLRenderer } from 'three'
import IslandViewerCanvas from '$vue/components/islands/Viewer/IslandViewerCanvas.vue'
import { useRenderLoop, useTresContext } from '@tresjs/core'
import { onMounted } from 'vue'

const context = useTresContext()
const { onLoop } = useRenderLoop()

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

// onLoop(({ delta, elapsed }) => {
//   context.scene.value.children[2].position.x = Math.sin(elapsed / 100) * 100
//   context.scene.value.children[2].position.y = Math.cos(elapsed / 100) * 100
// })
</script>

<style lang="scss" scoped></style>
