<template>
  <TresPerspectiveCamera :position="[8, 200, 32] as Vector3" :look-at="[0, 0, 0]"/>
  <CameraControls/>

  <Suspense>
    <IslandEditorTerrain
      v-if="islandEditorStore.targetTerrains[islandEditorStore.selectedTargetIsland] !== undefined"
      :terrain="islandEditorStore.targetTerrains[islandEditorStore.selectedTargetIsland]"/>
  </Suspense>

  <TresAmbientLight :intensity="1"/>
  <TresDirectionalLight :position="[10, 200, 100] as Vector3" cast-shadow :intensity="6" v-bind="{ color: 0xffdddd }"/>
</template>

<script setup lang="ts">
import {Vector3} from 'three'
import IslandEditorTerrain from '../Editor/IslandEditorTerrain.vue'
import {useIslandEditorStore} from '$store/IslandEditorStore.js'
import CameraControls from '$vue/components/islands/Camera/CameraControls.vue'
import {useTresContext} from "@tresjs/core";
import {onMounted} from "vue";

const islandEditorStore = useIslandEditorStore()

const context = useTresContext()

onMounted(() => {
  context.renderer.value.shadowMap.enabled = true
  context.scene.value.children[2].shadow.camera.right = 100
  context.scene.value.children[2].shadow.camera.left = -100
  context.scene.value.children[2].shadow.camera.top = -100
  context.scene.value.children[2].shadow.camera.bottom = 100
  context.scene.value.children[2].shadow.mapSize.width = 1000
  context.scene.value.children[2].shadow.mapSize.height = 1000
  context.scene.value.children[2].shadow.radius = 0.5
})

</script>

<style scoped lang="scss">
</style>
