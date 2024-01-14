<template>
  <primitive
    ref="objectRef"
    v-for="child of props.scene.children"
    :object="child"
    :scale="props.scale"
    :position="props.position"
    @click="(intersection, pointerEvent) => store.onMouseOverCell(pointerEvent, props.terrain)"
    @pointer-enter="(intersection, pointerEvent) => store.onMouseOverCell(pointerEvent, props.terrain)"
    @pointer-move="(intersection, pointerEvent) => store.onMouseOverCell(pointerEvent, props.terrain)"
    @pointer-leave="(intersection, pointerEvent) => store.onMouseLeaveCell(pointerEvent)"
    blocks-pointer-events></primitive>
</template>

<script setup lang="ts">
import { TresInstance } from '@tresjs/core'
import { Object3D, Vector3 } from 'three'

import { shallowRef, ShallowRef } from 'vue'
import { Terrain } from '$entity/Terrain'
import { useIslandViewerStore } from '$store/IslandViewerStore.js'

const store = useIslandViewerStore()

interface Props {
  terrain: Terrain
  position: Vector3
  scene: Object3D
  scale: number
}

const props = defineProps<Props>()

let objectRef: ShallowRef<TresInstance | null> = shallowRef(null)
</script>

<style lang="scss" scoped></style>
