<template>
  <primitive
    ref="objectRef"
    v-for="child of group.children"
    :object="child"
    :scale="getScale()"
    :position="[props.position[0], child.position.y + props.cell.data.elevation * 0.1 * child.userData.elevation_multiply, props.position[2]]"
    :rotation="getRotation()"
    receive-shadow
    cast-shadow
    @click="(intersection, pointerEvent) => store.onMouseOverCell(pointerEvent, props.cell)"
    @pointer-enter="(intersection, pointerEvent) => store.onMouseOverCell(pointerEvent, props.cell)"
    @pointer-move="(intersection, pointerEvent) => store.onMouseOverCell(pointerEvent, props.cell)"
    @pointer-leave="(intersection, pointerEvent) => store.onMouseLeaveCell(pointerEvent)"
    blocks-pointer-events></primitive>
</template>

<script setup lang="ts">
import {TresInstance} from '@tresjs/core'
import {Group, Object3D} from 'three'

import {shallowRef, ShallowRef} from 'vue'
import {useIslandViewerStore} from '$store/IslandViewerStore.js'
import {Cell, getRotation, getScale} from '$entity/Cell.js'

const store = useIslandViewerStore()

interface Props {
  cell: Cell
  position: Array<number>
  group: Object3D
}

const props = defineProps<Props>()

let objectRef: ShallowRef<TresInstance | null> = shallowRef(null)

let group = new Group()
for (let child of props.group.children) {
  group.add(child.clone())
}
</script>

<style lang="scss" scoped></style>
