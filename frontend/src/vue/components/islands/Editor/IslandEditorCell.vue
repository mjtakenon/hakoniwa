<template>
  <primitive
    v-for="child of props.scene.children"
    :object="child"
    :position="props.position"
    :scale="props.scale"
    @click="(intersection, pointerEvent) => islandEditorStore.onClickCell(pointerEvent, props.cell)"
    @pointer-enter="
      (intersection, pointerEvent) =>
        islandViewerStore.onMouseOverCell(pointerEvent, props.cell, islandEditorStore.isOpenPopup)
    "
    @pointer-move="
      (intersection, pointerEvent) =>
        islandViewerStore.onMouseOverCell(pointerEvent, props.cell, islandEditorStore.isOpenPopup)
    "
    @pointer-leave="(intersection, pointerEvent) => islandViewerStore.onMouseLeaveCell(pointerEvent)"
    blocks-pointer-events></primitive>
</template>

<script setup lang="ts">
import { Object3D, Vector3 } from 'three'
import { Terrain } from '$entity/Terrain'
import { useIslandEditorStore } from '$store/IslandEditorStore.js'
import { useIslandViewerStore } from '$store/IslandViewerStore.js'
import { Cell } from '$entity/Cell.js'

const islandEditorStore = useIslandEditorStore()
const islandViewerStore = useIslandViewerStore()

interface Props {
  cell: Cell
  position: Vector3
  scene: Object3D
  scale: number
}

const props = defineProps<Props>()
</script>

<style lang="scss" scoped></style>
