import { defineStore } from 'pinia'
import lodash from 'lodash'
import { Status } from './Entity/Status.js'
import { Hakoniwa } from './Entity/Hakoniwa.js'
import { Island } from './Entity/Island.js'
import { Terrain } from './Entity/Terrain.js'
import { Log } from './Entity/Log.js'
import { Plan } from './Entity/Plan.js'
import axios from 'axios'
import { Point } from './Entity/Point.js'
import { Turn } from './Entity/Turn.js'
import { AjaxResult, RequestStatus } from './Entity/Network.js'
import { Achievement } from './Entity/Achievement.js'

export interface PiniaState {
  hakoniwa: Hakoniwa
  island: Island
  terrains: Terrain[]
  targetTerrains: Array<Terrain[]>
  targetIslandComments: string[]
  status: Status
  logs: Array<Log>
  plans: Plan[]
  sentPlans: Plan[]
  targetIslands: Island[]
  selectedPoint: Point
  selectedPlanNumber: number
  selectedAmount: number
  selectedTargetIsland: number
  isPlanSent: boolean
  isSendingPlan: boolean
  planCandidate: Plan[]
  planSendingResult: number
  showNotification: boolean
  hoverWindowX: number
  hoverWindowY: number
  planWindowX: number
  planWindowY: number
  isMobile: boolean
  screenWidth: number
  showHoverWindow: boolean
  showPlanWindow: boolean
  hoverCellPoint: Point
  turn: Turn
  isOpenPopup: boolean
  isIslandPopupMount: boolean
  isIslandEditorMount: boolean
  isLoadingTerrain: boolean
  achievements: Achievement[]
}

export const useIslandEditorStore = defineStore('island-editor', {
  state: (): PiniaState => {
    return {
      hakoniwa: { width: 0, height: 0 },
      island: { id: 0, name: '', owner_name: '', comment: '' },
      terrains: [],
      targetTerrains: [],
      targetIslandComments: [],
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
      plans: [],
      sentPlans: [],
      targetIslands: [],
      selectedPoint: { x: 0, y: 0 },
      selectedPlanNumber: 1,
      selectedAmount: 0,
      selectedTargetIsland: 0,
      isPlanSent: true,
      isSendingPlan: false,
      planCandidate: [],
      planSendingResult: 200,
      showNotification: false,
      hoverWindowX: 100,
      hoverWindowY: 100,
      planWindowX: 100,
      planWindowY: 100,
      isMobile: document.documentElement.clientWidth < 1024,
      screenWidth: document.documentElement.clientWidth,
      showHoverWindow: false,
      showPlanWindow: false,
      hoverCellPoint: { x: 0, y: 0 },
      turn: {
        turn: 0,
        next_time: new Date('1970/1/1 00:00:00')
      },
      isOpenPopup: false,
      isIslandPopupMount: false,
      isIslandEditorMount: true,
      isLoadingTerrain: false,
      achievements: []
    }
  },
  getters: {
    getDefaultPlan(): Plan {
      return {
        key: 'cash_flow',
        data: {
          name: '資金繰り',
          point: {
            x: 0,
            y: 0
          },
          amount: 0,
          usePoint: false,
          useAmount: false,
          useTargetIsland: false,
          targetIsland: this.island.id,
          isFiring: false,
          priceString: '(+10億円)',
          amountString: '',
          defaultAmountString: ''
        }
      }
    },
    selectedTargetIslandName(): string {
      const target = this.targetIslands.find((island) => island.id === this.selectedTargetIsland)
      return target.name
    }
  },
  actions: {
    async putPlan() {
      console.debug('PUT', '/api/islands/' + this.island.id + '/plans')
      await axios
        .put('/api/islands/' + this.island.id + '/plans', {
          plan: JSON.stringify(this.plans)
        })
        .then((res) => {
          // sentPlanSuccess
          console.debug(res)
          this.isSendingPlan = false
          this.plans = res.data.plan
          this.sentPlans = lodash.cloneDeep(this.plans)
          this.planSendingResult = res.status
          this.showNotification = true
        })
        .catch((err) => {
          this.isSendingPlan = false
          this.planSendingResult = err.response.status
          this.showNotification = true
          console.error(err)
        })
    },
    async getIslandTerrain(id: number) {
      this.isLoadingTerrain = true
      const target = this.targetIslands.filter((island) => island.id === id)
      if (target.length < 1) throw new Error('存在しない島IDです')
      if (target.length > 1) throw new Error('targetIslandに島が重複しています')
      // 既にロード済みの場合
      if (target[0].terrains !== undefined) {
        this.isLoadingTerrain = false
        return
      }

      console.debug('GET', '/api/islands/' + id)
      await axios
        .get('/api/islands/' + id)
        .then((res) => {
          target[0].terrains = res.data.island.terrains
          target[0].comment = res.data.island.comment
          this.isLoadingTerrain = false
        })
        .catch((err) => {
          console.debug(err)
          throw new Error('島の地形取得時にエラーが発生しました')
        })
    },
    async postComment(comment: string): Promise<AjaxResult> {
      console.debug('POST', '/api/islands/' + this.island.id + '/comments')
      let result = {} as AjaxResult

      await axios
        .post('/api/islands/' + this.island.id + '/comments', {
          comment: comment
        })
        .then((res) => {
          this.island.comment = res.data.comment
          result.status = RequestStatus.Success
        })
        .catch((err) => {
          console.debug(err)
          result.status = RequestStatus.Failed
          result.error = err.response.status
        })
      return result
    }
  }
})
