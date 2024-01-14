<template>
  <primitive
    ref="objectRef"
    v-for="child of props.scene.children"
    :object="child"
    :scale="props.scale"
    :position="props.position"
    @click="(intersection, pointerEvent) => store.onMouseOverCell(pointerEvent, props.cell)"
    @pointer-enter="(intersection, pointerEvent) => store.onMouseOverCell(pointerEvent, props.cell)"
    @pointer-move="(intersection, pointerEvent) => store.onMouseOverCell(pointerEvent, props.cell)"
    @pointer-leave="(intersection, pointerEvent) => store.onMouseLeaveCell(pointerEvent)"
    blocks-pointer-events></primitive>
</template>

<script setup lang="ts">
import { TresInstance } from '@tresjs/core'
import { Object3D, Vector3 } from 'three'

import { shallowRef, ShallowRef } from 'vue'
import { useIslandViewerStore } from '$store/IslandViewerStore.js'
import { Cell } from '$entity/Cell.js'

const store = useIslandViewerStore()

interface Props {
  cell: Cell
  position: Vector3
  scene: Object3D
  scale: number
}

const props = defineProps<Props>()

let objectRef: ShallowRef<TresInstance | null> = shallowRef(null)
</script>

<style lang="scss" scoped></style>
