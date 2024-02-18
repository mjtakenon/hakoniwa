<template>
  <TresPerspectiveCamera :position="[8, 200, 32] as Vector3" :look-at="[0, 0, 0]" />
  <CameraControls />

  <Suspense>
    <IslandEditorTerrain
      v-if="islandEditorStore.targetTerrains[islandEditorStore.selectedTargetIsland] !== undefined"
      :terrain="islandEditorStore.targetTerrains[islandEditorStore.selectedTargetIsland]" />
  </Suspense>

  <TresAmbientLight :intensity="0.5" />
  <TresDirectionalLight :position="[10, 200, 100] as Vector3" cast-shadow :intensity="8" v-bind="{ color: 0xffdddd }" />
</template>

<script setup lang="ts">
import { onMounted, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { Vector3 } from 'three'
import { useIslandEditorStore } from '$store/IslandEditorStore.js'
import CameraControls from '$vue/components/islands/Camera/CameraControls.vue'
import IslandEditorTerrain from '$vue/components/islands/Editor/IslandEditorTerrain.vue'
import { useTresContext } from '@tresjs/core'

const islandEditorStore = useIslandEditorStore()
const { isOpenPopup, isLoadingTerrain } = storeToRefs(islandEditorStore)
const context = useTresContext()

watch(isLoadingTerrain, () => {
  if (islandEditorStore.isLoadingTerrain) return
  const target = islandEditorStore.targetIslands.filter(
    (island) => island.id === islandEditorStore.selectedTargetIsland
  )
  if (target.length < 1) throw new Error('対象の島が見つかりません')
  if (target[0].terrain === undefined) throw new Error('目標の島に地形情報がありません')

  // 取得した目標島の地形を保存
  islandEditorStore.$patch((state) => {
    state.targetTerrains[islandEditorStore.selectedTargetIsland] = {
      cells: target[0].terrain
    }
    state.targetIslandComments[islandEditorStore.selectedTargetIsland] = target[0].comment
  })
})

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
</script>

<style scoped lang="scss"></style>
