<template>
  <TresPerspectiveCamera :position="[8, 200, 32] as Vector3" :look-at="[0, 0, 0]"/>
  <CameraControls/>

  <Suspense>
    <IslandEditorTerrain
      v-if="islandEditorStore.targetTerrains[islandEditorStore.selectedTargetIsland] !== undefined"
      :terrain="islandEditorStore.targetTerrains[islandEditorStore.selectedTargetIsland]"/>
  </Suspense>

  <TresAmbientLight :intensity="2"/>
  <TresDirectionalLight :position="[192, 192, 192] as Vector3" cast-shadow :intensity="3"/>
</template>

<script setup lang="ts">
import {Vector3} from 'three'
import IslandEditorTerrain from '../Editor/IslandEditorTerrain.vue'
import {useIslandEditorStore} from '$store/IslandEditorStore.js'
import CameraControls from '$vue/components/islands/Camera/CameraControls.vue'

const islandEditorStore = useIslandEditorStore()

</script>

<style scoped lang="scss">
</style>
