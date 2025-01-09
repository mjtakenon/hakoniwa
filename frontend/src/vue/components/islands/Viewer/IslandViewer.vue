<template>
  <TresPerspectiveCamera :position="[4, 20, 8] as Vector3" :look-at="[0, 0, 0]"/>
  <CameraControls/>
  <Suspense>
    <IslandViewerTerrain/>
  </Suspense>

  <TresAmbientLight :intensity="1"/>
  <TresDirectionalLight :position="[20, 100, 100] as Vector3" cast-shadow :intensity="6" v-bind="{ color: 0xffdddd }"/>
</template>

<script setup lang="ts">
import {Vector3} from 'three'
import IslandViewerTerrain from '$vue/components/islands/Viewer/IslandViewerTerrain.vue'
import {useTresContext} from '@tresjs/core'
import {onMounted} from 'vue'
import CameraControls from '$vue/components/islands/Camera/CameraControls.vue'

const context = useTresContext()

onMounted(() => {
  context.renderer.value.shadowMap.enabled = true
  context.scene.value.children[2].shadow.camera.right = 20
  context.scene.value.children[2].shadow.camera.left = -15
  context.scene.value.children[2].shadow.camera.top = -15
  context.scene.value.children[2].shadow.camera.bottom = 15
  context.scene.value.children[2].shadow.mapSize.width = 4096
  context.scene.value.children[2].shadow.mapSize.height = 4096
  context.scene.value.children[2].shadow.radius = 0.5
  context.scene.value.children[2].shadow.bias = 0.000001
  context.scene.value.children[2].shadow.normalBias = 0.01
})

// useRenderLoop().onLoop(({ delta, elapsed }) => {
//   context.scene.value.children[2].position.x = Math.sin(elapsed / 10) * 100
//   context.scene.value.children[2].position.y = Math.cos(elapsed / 10) * 100
// })
</script>

<style lang="scss" scoped></style>
