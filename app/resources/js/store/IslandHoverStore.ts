import { defineStore } from 'pinia'
import { Point } from './Entity/Point.js'
import { Camera, Vector3 } from 'three'
import { ref } from 'vue'

export const useIslandHoverStore = defineStore('island-hover', () => {
  const point = ref<Point>({ x: 0, y: 0 })
  const camera = ref<Camera | null>(null)
  const cameraPositions = ref<Vector3[]>([])
  const cameraLookAt = ref<Vector3[]>([])

  const changeHoverCellCameraFocus = (type: string) => {
    camera.value.position.set(
      cameraPositions.value[type].x,
      cameraPositions.value[type].y,
      cameraPositions.value[type].z
    )
    camera.value.lookAt(cameraLookAt.value[type])
  }

  return { point, camera, cameraPositions, cameraLookAt, changeHoverCellCameraFocus }
})
