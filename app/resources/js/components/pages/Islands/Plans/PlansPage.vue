<template>
  <div id="plan-page">
    <StatusTable :island="store.island" :status="store.status" :achievements="store.achievements" />
    <CommentForm />
    <div class="mx-auto mt-10 flex flex-wrap items-stretch justify-center">
      <PlanController class="grow" :class="{ 'order-2': !canSideBySide }" />
      <div class="z-30" :class="{ 'order-1 w-full': !canSideBySide }">
        <PlansIslandEditor v-if="!store.isIslandPopupMount && !store.isOpenPopup" />
        <div v-else class="island-editor-padding"></div>
      </div>
      <PlanList class="grow" :class="{ 'order-2': !canSideBySide }"></PlanList>
    </div>
    <div class="md:max-lg:px-3">
      <Bbs></Bbs>
      <LogViewer :title="store.island.name + '島の近況'" :parsed-logs="store.logs" />
    </div>
    <IslandPopup v-if="!store.isIslandEditorMount && store.isOpenPopup" />
  </div>
</template>

<script setup lang="ts">
import StatusTable from '../../../islands/common/StatusTable.vue'
import LogViewer from '../../../islands/common/LogViewer.vue'
import PlansIslandEditor from './PlansIslandEditor.vue'
import PlanController from './PlansController.vue'
import PlanList from './PlansList.vue'
import lodash from 'lodash'
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useMainStore } from '../../../../store/MainStore'

import { Hakoniwa } from '../../../../store/Entity/Hakoniwa'
import { Island } from '../../../../store/Entity/Island'
import { Status } from '../../../../store/Entity/Status'
import { Terrain } from '../../../../store/Entity/Terrain'
import { Plan } from '../../../../store/Entity/Plan'
import { Turn } from '../../../../store/Entity/Turn'
import { LogParser, LogProps, SummaryProps } from '../../../../store/Entity/Log'
import IslandPopup from '../../../islands/Popup/IslandPopup.vue'
import CommentForm from '../../../islands/common/CommentForm.vue'
import { AchievementProp, getAchievementsList } from '../../../../store/Entity/Achievement'
import Bbs from '../../../islands/common/Bbs.vue'
import { BbsMessage } from '../../../../store/Entity/Bbs'
import { useIslandEditorStore } from '../../../../store/IslandEditorStore.js'

interface Props {
  hakoniwa: Hakoniwa
  // TODO: ここで飛んでくるislandはPlansController.phpで定義されており、js/store/Entity/Islandの中身と異なっている　共通化できないか？
  island: {
    id: number
    name: string
    owner_name: string
    status: Status
    terrains: Array<Terrain>
    plans: Array<Plan>
    logs: LogProps[]
    summary: SummaryProps[]
    comment?: string
    achievements: AchievementProp[]
    bbs: BbsMessage[]
  }
  planCandidate: { [K in string]: Plan['data'] }
  targetIslands: Island[]
  turn: {
    turn: number
    next_time: string
  }
}

const props = defineProps<Props>()

let hoverWindowTop = ref(170)
let hoverWindowLeft = ref(0)

const candidates: Plan[] = []

for (const [key, value] of Object.entries(props.planCandidate)) {
  candidates.push({
    key: key,
    data: value
  })
}

// turn.next_turnのDateオブジェクト変換
const turn: Turn = {
  turn: props.turn.turn,
  next_time: new Date(props.turn.next_time)
}

// Logsのparse
const parser = new LogParser()
const logs = parser.parse(props.island.logs, props.island.summary)

// Achievementの変換
const achievements = getAchievementsList(props.island.achievements)

// Pinia
const store = useIslandEditorStore()

store.$patch((state) => {
  state.hakoniwa = props.hakoniwa
  state.island = {
    id: props.island.id,
    name: props.island.name,
    owner_name: props.island.owner_name,
    comment: props.island.comment,
    terrains: props.island.terrains
  }
  state.status = props.island.status
  state.terrains = props.island.terrains
  state.logs = logs
  state.plans = lodash.cloneDeep(props.island.plans)
  state.sentPlans = lodash.cloneDeep(props.island.plans)
  state.planCandidate = candidates
  state.targetIslands = props.targetIslands
  state.selectedTargetIsland = props.island.id
  state.turn = turn
  state.achievements = achievements
  state.bbs = props.island.bbs
})

const canSideBySide = computed(() => {
  return store.screenWidth > 912
})

onMounted(() => {
  window.addEventListener('resize', onWindowSizeChanged)
})
onUnmounted(() => {
  window.removeEventListener('resize', onWindowSizeChanged)
})

const onWindowSizeChanged = () => {
  const newScreenWidth = document.documentElement.clientWidth
  if (store.screenWidth !== newScreenWidth) {
    store.screenWidth = newScreenWidth
  }
}
</script>

<style lang="scss" scoped>
#plan-page {
  @apply mx-auto min-h-[1200px] max-w-[1000px] text-center;

  .island-editor-padding {
    margin: 0 auto;
    @apply w-full max-w-[496px] md:min-w-[496px];
  }
}
</style>
