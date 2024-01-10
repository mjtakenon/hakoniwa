import { defineStore } from 'pinia'
import lodash from 'lodash'
import { Status } from '$entity/Status.js'
import { Hakoniwa } from '$entity/Hakoniwa.js'
import { Island } from '$entity/Island.js'
import { Terrain } from '$entity/Terrain.js'
import { Log } from '$entity/Log.js'
import { Plan } from '$entity/Plan.js'
import axios from 'axios'
import { Point } from '$entity/Point.js'
import { Turn } from '$entity/Turn.js'
import { AjaxResult, RequestStatus } from '$entity/Network.js'
import { Achievement } from '$entity/Achievement.js'
import { computed, ref } from 'vue'

export const useIslandEditorStore = defineStore('island-editor', () => {
  const hakoniwa = ref<Hakoniwa>({ width: 0, height: 0 })
  const island = ref<Island>({ id: 0, name: '', owner_name: '', comment: '' })
  const terrains = ref<Terrain[]>([])
  const targetTerrains = ref<Array<Terrain[]>>([])
  const targetIslandComments = ref<string[]>([])
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
  const plans = ref<Plan[]>([])
  const sentPlans = ref<Plan[]>([])
  const targetIslands = ref<Island[]>([])
  const selectedPoint = ref<Point>({ x: 0, y: 0 })
  const selectedPlanNumber = ref(1)
  const selectedAmount = ref(0)
  const selectedTargetIsland = ref(0)
  const isPlanSent = ref(true)
  const isSendingPlan = ref(false)
  const planCandidate = ref<Plan[]>([])
  const planSendingResult = ref(200)
  const showNotification = ref(false)
  const hoverWindow = ref<Point>({ x: 100, y: 100 })
  const planWindow = ref<Point>({ x: 100, y: 100 })
  const isMobile = ref(document.documentElement.clientWidth < 1024)
  const screenWidth = ref(document.documentElement.clientWidth)
  const showHoverWindow = ref(false)
  const showPlanWindow = ref(false)
  const hoverCellPoint = ref<Point>({ x: 0, y: 0 })
  const turn = ref<Turn>({
    turn: 0,
    next_time: new Date('1970/1/1 00:00:00')
  })
  const isOpenPopup = ref(false)
  const isIslandPopupMount = ref(false)
  const isIslandEditorMount = ref(true)
  const isLoadingTerrain = ref(false)
  const achievements = ref<Achievement[]>([])

  const getDefaultPlan = computed<Plan>(() => {
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
        targetIsland: island.value.id,
        isFiring: false,
        priceString: '(+10億円)',
        amountString: '',
        defaultAmountString: ''
      }
    }
  })

  const selectedTargetIslandName = computed(() => {
    const target = targetIslands.value.find((island) => island.id === selectedTargetIsland.value)
    return target.name
  })

  const putPlan = async () => {
    console.debug('PUT', '/api/islands/' + island.value.id + '/plans')
    await axios
      .put('/api/islands/' + island.value.id + '/plans', {
        plan: JSON.stringify(plans.value)
      })
      .then((res) => {
        // sentPlanSuccess
        console.debug(res)
        isSendingPlan.value = false
        plans.value = res.data.plan
        sentPlans.value = lodash.cloneDeep(plans.value)
        planSendingResult.value = res.status
        showNotification.value = true
      })
      .catch((err) => {
        isSendingPlan.value = false
        planSendingResult.value = err.response.status
        showNotification.value = true
        console.error(err)
      })
  }

  const getIslandTerrain = async (id: number) => {
    isLoadingTerrain.value = true
    const target = targetIslands.value.filter((island) => island.id === id)
    if (target.length < 1) throw new Error('存在しない島IDです')
    if (target.length > 1) throw new Error('targetIslandに島が重複しています')
    // 既にロード済みの場合
    if (target[0].terrains !== undefined) {
      isLoadingTerrain.value = false
      return
    }

    console.debug('GET', '/api/islands/' + id)
    await axios
      .get('/api/islands/' + id)
      .then((res) => {
        target[0].terrains = res.data.island.terrains
        target[0].comment = res.data.island.comment
        isLoadingTerrain.value = false
      })
      .catch((err) => {
        console.debug(err)
        throw new Error('島の地形取得時にエラーが発生しました')
      })
  }

  const postComment = async (comment: string): Promise<AjaxResult> => {
    console.debug('POST', '/api/islands/' + island.value.id + '/comments')
    let result = {} as AjaxResult

    await axios
      .post('/api/islands/' + island.value.id + '/comments', {
        comment: comment
      })
      .then((res) => {
        island.value.comment = res.data.comment
        result.status = RequestStatus.Success
      })
      .catch((err) => {
        console.debug(err)
        result.status = RequestStatus.Failed
        result.error = err.response.status
      })
    return result
  }

  return {
    hakoniwa,
    island,
    terrains,
    targetTerrains,
    targetIslandComments,
    status,
    logs,
    plans,
    sentPlans,
    targetIslands,
    selectedPoint,
    selectedPlanNumber,
    selectedAmount,
    selectedTargetIsland,
    isPlanSent,
    isSendingPlan,
    planCandidate,
    planSendingResult,
    showNotification,
    hoverWindow,
    planWindow,
    isMobile,
    screenWidth,
    showHoverWindow,
    showPlanWindow,
    hoverCellPoint,
    turn,
    isOpenPopup,
    isIslandPopupMount,
    isIslandEditorMount,
    isLoadingTerrain,
    achievements,
    getDefaultPlan,
    selectedTargetIslandName,
    putPlan,
    getIslandTerrain,
    postComment
  }
})
