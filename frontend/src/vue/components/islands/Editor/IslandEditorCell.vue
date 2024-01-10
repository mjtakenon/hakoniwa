<template>
  <primitive
    v-for="child of props.scene.children"
    :object="child"
    :position="props.position"
    :scale="props.scale"
    @click="(intersection, pointerEvent) => onClickCell(pointerEvent)"
    @pointer-enter="(intersection, pointerEvent) => onMouseOverCell(pointerEvent)"
    @pointer-move="(intersection, pointerEvent) => onMouseOverCell(pointerEvent)"
    @pointer-leave="(intersection, pointerEvent) => onMouseLeaveCell(pointerEvent)"
    blocks-pointer-events></primitive>
</template>

<script setup lang="ts">
import { Object3D, Vector3 } from 'three'
import { Terrain } from '$js/entity//Terrain'
import { useIslandEditorStore } from '$store/IslandEditorStore.js'
import { useIslandHoverStore } from '$store/IslandHoverStore.js'

const store = useIslandEditorStore()

interface Props {
  terrain: Terrain
  position: Vector3
  scene: Object3D
  scale: number
}

const props = defineProps<Props>()

const onMouseOverCell = (event: MouseEvent) => {
  onMouseMoveCell(event)

  store.showHoverWindow = true
  store.hoverCellPoint = props.terrain.data.point

  useIslandHoverStore().changeHoverCellCameraFocus(props.terrain.type)
}

const onMouseMoveCell = (event: MouseEvent) => {
  const offsetY = 25
  if (store.isOpenPopup) {
    store.hoverWindow.y = document.documentElement.clientHeight - (event.pageY - window.scrollY) + offsetY
  } else {
    store.hoverWindow.y = document.documentElement.clientHeight - event.pageY + offsetY
  }
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

const onClickCell = (event: MouseEvent) => {
  if (
    store.showPlanWindow &&
    store.selectedPoint.x === props.terrain.data.point.x &&
    store.selectedPoint.y === props.terrain.data.point.y
  ) {
    store.showPlanWindow = false
    return
  }
  store.selectedPoint.x = props.terrain.data.point.x
  store.selectedPoint.y = props.terrain.data.point.y
  store.showPlanWindow = true

  if (store.isMobile) {
    store.planWindow.x = event.pageX
    const offsetX = 15
    const offsetY = 30
    const elementWidth = 230
    const leftEdge = store.planWindow.x - elementWidth / 2
    const rightEdge = store.planWindow.x + elementWidth / 2
    if (leftEdge < offsetX) {
      store.planWindow.x += -leftEdge + offsetX
    } else if (rightEdge > store.screenWidth) {
      store.planWindow.x -= rightEdge - store.screenWidth + offsetX
    }

    if (store.isOpenPopup) {
      store.planWindow.y = event.pageY - window.scrollY + offsetY
    } else {
      store.planWindow.y = event.pageY + offsetY
    }
  } else {
    const offset = 15
    store.planWindow.x = event.pageX + offset
    if (store.isOpenPopup) {
      store.planWindow.y = event.pageY - window.scrollY + offset
    } else {
      store.planWindow.y = event.pageY + offset
    }
  }
}
</script>

<style lang="scss" scoped></style>
