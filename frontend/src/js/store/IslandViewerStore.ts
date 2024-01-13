import { defineStore } from 'pinia'
import { Status } from '$entity/Status.js'
import { Hakoniwa } from '$entity/Hakoniwa.js'
import { Island } from '$entity/Island.js'
import { Terrain } from '$entity/Terrain.js'
import { Log } from '$entity/Log.js'
import { Point } from '$entity/Point.js'
import { Achievement } from '$entity/Achievement.js'
import { ref } from 'vue'
import { useIslandHoverStore } from '$store/IslandHoverStore.js'

export const useIslandViewerStore = defineStore('island-viewer', () => {
  const hakoniwa = ref<Hakoniwa>({ width: 0, height: 0 })
  const island = ref<Island>({ id: 0, name: '', owner_name: '', comment: '' })
  const terrains = ref<Terrain[]>([])
  const status = ref<Status>({
    area: 0,
    development_points: 0,
    environment: 'best',
    foods: 0,
    foods_production_capacity: 0,
    funds: 0,
    funds_production_capacity: 0,
    population: 0,
    resources: 0,
    resources_production_capacity: 0,
    maintenance_number_of_people: 0,
    abandonment_turn: 0
  })
  const logs = ref<Log[]>([])
  const hoverWindow = ref<Point>({ x: 100, y: 100 })
  const isMobile = ref(document.documentElement.clientWidth < 1024)
  const screenWidth = ref(document.documentElement.clientWidth)
  const showHoverWindow = ref(false)
  const hoverCellPoint = ref<Point>({ x: 0, y: 0 })
  const achievements = ref<Achievement[]>([])

  const onMouseOverCell = (event: MouseEvent, terrain: Terrain) => {
    onMouseMoveCell(event)

    showHoverWindow.value = true
    hoverCellPoint.value = terrain.data.point

    useIslandHoverStore().changeHoverCellCameraFocus(terrain.type)
  }

  const onMouseMoveCell = (event: MouseEvent) => {
    const offsetY = 25
    hoverWindow.value.y = document.documentElement.clientHeight - event.pageY + offsetY
    hoverWindow.value.x = event.pageX

    // Screen Overflow Check
    if (isMobile.value) {
      const windowSize = 200
      const paddingOffset = 20
      const leftEdge = hoverWindow.value.x - windowSize / 2
      const rightEdge = hoverWindow.value.x + windowSize / 2
      if (leftEdge < paddingOffset) {
        hoverWindow.value.x += -leftEdge + paddingOffset
      } else if (rightEdge > screenWidth.value) {
        hoverWindow.value.x -= rightEdge - screenWidth.value + paddingOffset
      }
    }
  }

  const onMouseLeaveCell = (event: MouseEvent) => {
    showHoverWindow.value = false
  }

  return {
    hakoniwa,
    island,
    terrains,
    status,
    logs,
    hoverWindow,
    isMobile,
    screenWidth,
    showHoverWindow,
    hoverCellPoint,
    achievements,
    onMouseOverCell,
    onMouseMoveCell,
    onMouseLeaveCell
  }
})
