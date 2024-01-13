<template>
  <primitive
    v-for="child of props.scene.children"
    :object="child"
    :position="props.position"
    :scale="props.scale"
    @click="(intersection, pointerEvent) => store.onClickCell(pointerEvent, props.terrain)"
    @pointer-enter="(intersection, pointerEvent) => store.onMouseOverCell(pointerEvent, props.terrain)"
    @pointer-move="(intersection, pointerEvent) => store.onMouseOverCell(pointerEvent, props.terrain)"
    @pointer-leave="(intersection, pointerEvent) => store.onMouseLeaveCell(pointerEvent)"
    blocks-pointer-events></primitive>
</template>

<script setup lang="ts">
import { Object3D, Vector3 } from 'three'
import { Terrain } from '$entity/Terrain'
import { useIslandEditorStore } from '$store/IslandEditorStore.js'

const store = useIslandEditorStore()

interface Props {
  terrain: Terrain
  position: Vector3
  scene: Object3D
  scale: number
}

const props = defineProps<Props>()
</script>

<style lang="scss" scoped></style>
