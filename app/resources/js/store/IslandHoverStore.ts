import { defineStore } from 'pinia'
import { Plan } from './Entity/Plan.js'
import { Point } from './Entity/Point.js'
import { Camera, Vector3 } from 'three'

export interface PiniaState {
  hoverCellPoint: Point
  hoverCellCamera: Camera | null
  hoverCellCameraPositions: Vector3[]
  hoverCellCameraLookAt: Vector3[]
}

export const useIslandHoverStore = defineStore('island-hover', {
  state: (): PiniaState => {
    return {
      hoverCellPoint: { x: 0, y: 0 },
      hoverCellCamera: null,
      hoverCellCameraPositions: [],
      hoverCellCameraLookAt: []
    }
  },
  getters: {},
  actions: {
    changeHoverCellCameraFocus(type: string) {
      this.hoverCellCamera.position.set(
        this.hoverCellCameraPositions[type].x,
        this.hoverCellCameraPositions[type].y,
        this.hoverCellCameraPositions[type].z
      )
      this.hoverCellCamera.lookAt(this.hoverCellCameraLookAt[type])
    }
  }
})
