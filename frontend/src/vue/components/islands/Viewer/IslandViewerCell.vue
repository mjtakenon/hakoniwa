<template>
  <primitive
    ref="objectRef"
    v-for="child of props.scene.children"
    :object="child"
    :scale="props.scale"
    :position="props.position"
    @pointer-enter="(intersection, pointerEvent) => onMouseOverCell(pointerEvent)"
    @pointer-move="(intersection, pointerEvent) => onMouseOverCell(pointerEvent)"
    @pointer-leave="(intersection, pointerEvent) => onMouseLeaveCell(pointerEvent)"
    blocks-pointer-events></primitive>
</template>

<script setup lang="ts">
import { TresInstance } from '@tresjs/core'
import { Object3D, Vector3 } from 'three'

import { shallowRef, ShallowRef } from 'vue'
import { Terrain } from '$js/entity/Terrain'
import { useIslandViewerStore } from '$store/IslandViewerStore.js'
import { useIslandHoverStore } from '$store/IslandHoverStore.js'

const store = useIslandViewerStore()

interface Props {
  terrain: Terrain
  position: Vector3
  scene: Object3D
  scale: number
}

const props = defineProps<Props>()

let objectRef: ShallowRef<TresInstance | null> = shallowRef(null)

const onMouseOverCell = (event: MouseEvent) => {
  onMouseMoveCell(event)

  store.showHoverWindow = true
  store.hoverCellPoint = props.terrain.data.point

  useIslandHoverStore().changeHoverCellCameraFocus(props.terrain.type)
}

const onMouseMoveCell = (event: MouseEvent) => {
  const offsetY = 25
  store.hoverWindow.y = document.documentElement.clientHeight - event.pageY + offsetY
  store.hoverWindow.x = event.pageX

  // Screen Overflow Check
  if (store.isMobile) {
    const windowSize = 200
    const paddingOffset = 20
    const leftEdge = store.hoverWindow.x - windowSize / 2
    const rightEdge = store.hoverWindow.x + windowSize / 2
    if (leftEdge < paddingOffset) {
      store.hoverWindow.x += -leftEdge + paddingOffset
    } else if (rightEdge > store.screenWidth) {
      store.hoverWindow.x -= rightEdge - store.screenWidth + paddingOffset
    }
  }
}

const onMouseLeaveCell = (event: MouseEvent) => {
  store.showHoverWindow = false
}
</script>

<style lang="scss" scoped></style>
