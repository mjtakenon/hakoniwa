<template>
  <primitive
    ref="objectRef"
    v-for="child of group.children"
    :object="child"
    :rotation="getRotation(props.edge)"
    :scale="getScale()"
    :position="[position[0], child.position.y, position[2]]"
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


let objectRef: ShallowRef<TresInstance | null> = shallowRef(null)

let group = new Group()
group.add(props.group.children[0].clone(false))
</script>

<style lang="scss" scoped></style>
