<template>
  <primitive
    v-for="child of group.children"
    :object="child"
    :rotation="getRotation(props.edge)"
    :scale="getScale()"
    :position="[position[0], child.position.y + props.edge.data.elevation * 0.1 * child.userData.elevation_multiply, position[2]]"
    receive-shadow
    cast-shadow
    blocks-pointer-events></primitive>
</template>

<script setup lang="ts">
import {TresInstance} from '@tresjs/core'
import {Group, Object3D} from 'three'

import {shallowRef, ShallowRef} from 'vue'
import {useIslandViewerStore} from '$store/IslandViewerStore.js'
import {Edge, getPosition, getRotation, getScale} from '$entity/Edge.js'

const store = useIslandViewerStore()

interface Props {
  edge: Edge
  position: Array<number>
  group: Object3D
}

const props = defineProps<Props>()

let position = getPosition(props.edge, props.position)

let group = new Group()
for (let child of props.group.children) {
  group.add(child.clone())
}
</script>

<style lang="scss" scoped></style>
