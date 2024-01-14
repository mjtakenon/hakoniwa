import { defineStore } from 'pinia'
import lodash from 'lodash'
import { Island } from '$entity/Island.js'
import { Terrain } from '$entity/Terrain.js'
import { Plan } from '$entity/Plan.js'
import axios from 'axios'
import { Point } from '$entity/Point.js'
import { Turn } from '$entity/Turn.js'
import { AjaxResult, RequestStatus } from '$entity/Network.js'
import { Achievement } from '$entity/Achievement.js'
import { computed, ref } from 'vue'
import { useIslandViewerStore } from '$store/IslandViewerStore.js'

export const useIslandEditorStore = defineStore('island-editor', () => {
  const targetTerrains = ref<Array<Terrain[]>>([])
  const targetIslandComments = ref<string[]>([])
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
  const planWindow = ref<Point>({ x: 100, y: 100 })
  const showPlanWindow = ref(false)
  const turn = ref<Turn>({
    turn: 0,
    next_time: new Date('1970/1/1 00:00:00')
  })
  const isOpenPopup = ref(false)
  const isIslandPopupMount = ref(false)
  const isIslandEditorMount = ref(true)
  const isLoadingTerrain = ref(false)
  const achievements = ref<Achievement[]>([])
  const islandViewerStore = useIslandViewerStore()

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
        targetIsland: islandViewerStore.island.id,
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
    console.debug('PUT', '/api/islands/' + islandViewerStore.island.id + '/plans')
    await axios
      .put('/api/islands/' + islandViewerStore.island.id + '/plans', {
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
    console.debug('POST', '/api/islands/' + islandViewerStore.island.id + '/comments')
    let result = {} as AjaxResult

    await axios
      .post('/api/islands/' + islandViewerStore.island.id + '/comments', {
        comment: comment
      })
      .then((res) => {
        islandViewerStore.island.comment = res.data.comment
        result.status = RequestStatus.Success
      })
      .catch((err) => {
        console.debug(err)
        result.status = RequestStatus.Failed
        result.error = err.response.status
      })
    return result
  }

  const onClickCell = (event: MouseEvent, terrain: Terrain) => {
    if (
      showPlanWindow.value &&
      selectedPoint.value.x === terrain.data.point.x &&
      selectedPoint.value.y === terrain.data.point.y
    ) {
      showPlanWindow.value = false
      return
    }
    selectedPoint.value.x = terrain.data.point.x
    selectedPoint.value.y = terrain.data.point.y
    showPlanWindow.value = true

    if (islandViewerStore.isMobile) {
      planWindow.value.x = event.pageX
      const offsetX = 15
      const offsetY = 30
      const elementWidth = 230
      const leftEdge = planWindow.value.x - elementWidth / 2
      const rightEdge = planWindow.value.x + elementWidth / 2
      if (leftEdge < offsetX) {
        planWindow.value.x += -leftEdge + offsetX
      } else if (rightEdge > islandViewerStore.screenWidth) {
        planWindow.value.x -= rightEdge - islandViewerStore.screenWidth + offsetX
      }

      if (isOpenPopup.value) {
        planWindow.value.y = event.pageY - window.scrollY + offsetY
      } else {
        planWindow.value.y = event.pageY + offsetY
      }
    } else {
      const offset = 15
      planWindow.value.x = event.pageX + offset
      if (isOpenPopup.value) {
        planWindow.value.y = event.pageY - window.scrollY + offset
      } else {
        planWindow.value.y = event.pageY + offset
      }
    }
  }

  return {
    targetTerrains,
    targetIslandComments,
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
    planWindow,
    showPlanWindow,
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
    postComment,
    onClickCell
  }
})
