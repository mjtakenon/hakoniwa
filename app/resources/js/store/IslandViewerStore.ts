import { defineStore } from 'pinia'
import { Status } from './Entity/Status.js'
import { Hakoniwa } from './Entity/Hakoniwa.js'
import { Island } from './Entity/Island.js'
import { Terrain } from './Entity/Terrain.js'
import { Log } from './Entity/Log.js'
import { Point } from './Entity/Point.js'
import { Achievement } from './Entity/Achievement.js'

export interface PiniaState {
  hakoniwa: Hakoniwa
  island: Island
  terrains: Terrain[]
  status: Status
  logs: Array<Log>
  hoverWindowX: number
  hoverWindowY: number
  isMobile: boolean
  screenWidth: number
  showHoverWindow: boolean
  hoverCellPoint: Point
  achievements: Achievement[]
}

export const useIslandViewerStore = defineStore('island-viewer', {
  state: (): PiniaState => {
    return {
      hakoniwa: { width: 0, height: 0 },
      island: { id: 0, name: '', owner_name: '', comment: '' },
      terrains: [],
      status: {
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
      },
      logs: [],
      hoverWindowX: 100,
      hoverWindowY: 100,
      isMobile: document.documentElement.clientWidth < 1024,
      screenWidth: document.documentElement.clientWidth,
      showHoverWindow: false,
      hoverCellPoint: { x: 0, y: 0 },
      achievements: []
    }
  },
  getters: {},
  actions: {}
})
