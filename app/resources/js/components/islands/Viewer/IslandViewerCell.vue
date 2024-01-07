<template>
  <primitive
    ref="objectRef"
    v-for="child of props.scene.children"
    :object="child"
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
import { Terrain } from '../../../store/Entity/Terrain'
import { useMainStore } from '../../../store/MainStore'

const store = useMainStore()

interface Props {
  terrain: Terrain
  position: Vector3
  scene: Object3D
}

const props = defineProps<Props>()

let objectRef: ShallowRef<TresInstance | null> = shallowRef(null)

const onMouseOverCell = (event: MouseEvent) => {
  onMouseMoveCell(event)

  store.showHoverWindow = true
  store.hoverCellPoint = props.terrain.data.point

  store.changeHoverCellCameraFocus(props.terrain.type)
}

const onMouseMoveCell = (event: MouseEvent) => {
  const offsetY = 25
  store.hoverWindowY = document.documentElement.clientHeight - event.pageY + offsetY
  store.hoverWindowX = event.pageX

  // Screen Overflow Check
  if (store.isMobile) {
    const windowSize = 200
    const paddingOffset = 20
    const leftEdge = store.hoverWindowX - windowSize / 2
    const rightEdge = store.hoverWindowX + windowSize / 2
    if (leftEdge < paddingOffset) {
      store.hoverWindowX += -leftEdge + paddingOffset
    } else if (rightEdge > store.screenWidth) {
      store.hoverWindowX -= rightEdge - store.screenWidth + paddingOffset
    }
  }
}

const onMouseLeaveCell = (event: MouseEvent) => {
  store.showHoverWindow = false
}
</script>

<style lang="scss" scoped></style>
