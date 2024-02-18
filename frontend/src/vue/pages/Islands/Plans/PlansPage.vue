<template>
  <div id="plan-page">
    <StatusTable
      :island="islandViewerStore.island"
      :status="islandViewerStore.status"
      :achievements="islandEditorStore.achievements" />
    <CommentForm />
    <div class="mx-auto mt-10 flex flex-wrap items-stretch justify-center">
      <PlanController class="grow" :class="{ 'order-2': !canSideBySide }" />
      <div class="z-30" :class="{ 'order-1 w-full': !canSideBySide }">
        <div id="island">
          <PlansIslandCanvas v-if="!islandEditorStore.isIslandPopupMount && !islandEditorStore.isOpenPopup" />
          <CountdownWidget></CountdownWidget>
          <IslandHoverWindow
            :showHoverWindow="islandViewerStore.showHoverWindow"
            :hoverWindowPoint="islandViewerStore.hoverWindowPoint"
            :hoverCellPoint="islandViewerStore.hoverCellPoint"
            :terrain="islandViewerStore.terrain">
            <template v-for="(plan, index) of islandEditorStore.plans">
              <div
                class="hover-window-plan"
                v-if="
                  plan.data.usePoint &&
                  plan.data.point.x === islandViewerStore.hoverCellPoint.x &&
                  plan.data.point.y === islandViewerStore.hoverCellPoint.y &&
                  (!plan.data.useTargetIsland ||
                    (plan.data.useTargetIsland && plan.data.targetIsland === islandViewerStore.island.id))
                ">
                <span>[{{ index + 1 }}] </span>
                <span>{{ plan.data.name }}</span>
                <span v-if="plan.data.useAmount">
                  <span v-if="plan.data.amount === 0"> {{ plan.data.defaultAmountString }}</span>
                  <span v-else> {{ plan.data.amountString.replace(':amount:', plan.data.amount.toString()) }} </span>
                </span>
              </div>
            </template>
          </IslandHoverWindow>
          <PlanWindow />
        </div>
      </div>
      <PlanList class="grow" :class="{ 'order-2': !canSideBySide }"></PlanList>
    </div>
    <div class="md:max-lg:px-3">
      <Bbs :island="islandViewerStore.island" />
      <LogViewer :title="islandViewerStore.island.name + '島の近況'" :parsed-logs="islandViewerStore.logs" />
    </div>
    <PlansIslandPopupCanvas v-if="!islandEditorStore.isIslandEditorMount && islandEditorStore.isOpenPopup" />
  </div>
</template>

<script setup lang="ts">
import StatusTable from '$vue/components/islands/common/StatusTable.vue'
import LogViewer from '$vue/components/islands/common/LogViewer.vue'
import PlanController from './PlansController.vue'
import PlanList from './PlansList.vue'
import lodash from 'lodash'
import { computed, onMounted, onUnmounted, reactive } from 'vue'

import { Hakoniwa } from '$entity/Hakoniwa'
import { Island } from '$entity/Island'
import { Status } from '$entity/Status'
import { Plan } from '$entity/Plan'
import { Turn } from '$entity/Turn'
import { LogParser, LogProps, SummaryProps } from '$entity/Log'
import CommentForm from '$vue/pages/Islands/Plans/PlansCommentForm.vue'
import { AchievementProp, getAchievementsList } from '$entity/Achievement'
import Bbs from '$vue/components/islands/common/Bbs.vue'
import { BbsMessage } from '$entity/Bbs'
import { useIslandEditorStore } from '$store/IslandEditorStore.js'
import { useBbsStore } from '$store/BbsStore.js'
import { useIslandViewerStore } from '$store/IslandViewerStore.js'
import { Cell } from '$entity/Cell.js'
import CountdownWidget from '$vue/components/islands/common/CountdownWidget.vue'
import IslandHoverWindow from '$vue/components/islands/Hover/IslandHoverWindow.vue'
import PlanWindow from '$vue/components/islands/Editor/IslandEditorPlanWindow.vue'
import { NoToneMapping, SRGBColorSpace, VSMShadowMap } from 'three'
import PlansIslandCanvas from '$vue/pages/Islands/Plans/PlansIslandEditorCanvas.vue'
import PlansIslandPopupCanvas from '$vue/pages/Islands/Plans/PlansIslandPopupCanvas.vue'

interface Props {
  hakoniwa: Hakoniwa
  // TODO: ここで飛んでくるislandはPlansController.phpで定義されており、js/entity/Islandの中身と異なっている　共通化できないか？
  island: {
    id: number
    name: string
    owner_name: string
    status: Status
    terrain: Cell[]
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

const gl = reactive({
  clearColor: '#888888',
  shadows: true,
  alpha: true,
  shadowMapType: VSMShadowMap,
  outputColorSpace: SRGBColorSpace,
  toneMapping: NoToneMapping
})

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
const islandEditorStore = useIslandEditorStore()
const islandViewerStore = useIslandViewerStore()

islandEditorStore.$patch((state) => {
  state.plans = lodash.cloneDeep(props.island.plans)
  state.sentPlans = lodash.cloneDeep(props.island.plans)
  state.planCandidate = candidates
  state.targetIslands = props.targetIslands
  state.selectedTargetIsland = props.island.id
  state.turn = turn
  state.achievements = achievements
})

islandViewerStore.$patch((state) => {
  state.hakoniwa = props.hakoniwa
  state.island = {
    id: props.island.id,
    name: props.island.name,
    owner_name: props.island.owner_name,
    comment: props.island.comment,
    terrain: {
      cells: props.island.terrain
    }
  }
  state.status = props.island.status
  state.terrain = {
    cells: props.island.terrain
  }
  state.logs = logs
  state.achievements = getAchievementsList(props.island.achievements)
})

useBbsStore().bbs = props.island.bbs

const canSideBySide = computed(() => {
  return islandViewerStore.screenWidth > 912
})

onMounted(() => {
  window.addEventListener('resize', onWindowSizeChanged)
})
onUnmounted(() => {
  window.removeEventListener('resize', onWindowSizeChanged)
})

const onWindowSizeChanged = () => {
  const newScreenWidth = document.documentElement.clientWidth
  if (islandViewerStore.screenWidth !== newScreenWidth) {
    islandViewerStore.screenWidth = newScreenWidth
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

.island-canvas {
  @apply max-h-[496px] min-h-[496px] w-full;
}

#island {
  margin: 0 auto;
  @apply w-full max-w-[496px] md:min-w-[496px];

  .row {
    @apply m-0 -mt-[0.1px] bg-black p-0;
    display: grid;

    .cell {
      @apply aspect-square w-full;
    }

    &:nth-child(odd) {
      grid-template-columns: 1fr repeat(15, 2fr);
    }

    &:nth-child(even) {
      grid-template-columns: repeat(15, 2fr) 1fr;
    }

    .cell-is-selected {
      border: 1px solid white;
    }

    .cell-is-referenced {
      border: 1px solid red;
    }

    .left-padding {
      @apply z-10 aspect-[1/2] w-full;
      background-image: url('/img/hakoniwa/hakogif/land0.gif');
      background-position: left;
    }

    .right-padding {
      @apply relative z-10 aspect-[1/2] w-full;
      background-image: url('/img/hakoniwa/hakogif/land0.gif');
      background-position: right;

      .right-padding-text {
        @apply absolute left-1 z-10 overflow-hidden text-xs leading-none text-white max-xs:hidden md:text-sm;
      }
    }
  }

  .hover-window-plan {
    @apply m-0 p-0 text-left text-sm;
  }

  .hover-window-plan:nth-child(2) {
    @apply mt-3 border-t border-gray-500 border-opacity-70 pt-2;
  }
}
</style>
