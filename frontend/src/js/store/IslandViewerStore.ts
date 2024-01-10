import { defineStore } from 'pinia'
import { Status } from '$js/entity/Status.js'
import { Hakoniwa } from '$js/entity/Hakoniwa.js'
import { Island } from '$js/entity/Island.js'
import { Terrain } from '$js/entity/Terrain.js'
import { Log } from '$js/entity/Log.js'
import { Point } from '$js/entity/Point.js'
import { Achievement } from '$js/entity/Achievement.js'
import { ref } from 'vue'

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
    achievements
  }
})
