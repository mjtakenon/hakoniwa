<template>
  <div id="plan-controller">
    <div class="mb-4">
      <div class="section-header">動作</div>
      <div class="action">
        <button class="action-button dark:button-variant-reverse" @click="onClickInsert()">挿入</button>
        <button class="action-button dark:button-variant-reverse" @click="onClickOverwrite()">上書き</button>
        <button class="action-button dark:button-variant-reverse" @click="onClickDelete()">削除</button>
      </div>
    </div>

    <div class="mb-4">
      <div class="section-header">開発計画</div>
      <div class="dev-plan">
        <p class="title-sm-inline">計画番号:</p>
        <select class="float-right" v-model="store.selectedPlanNumber">
          <option v-for="num of MAX_PLAN_NUMBER" :value="num" :key="num">{{ num }}</option>
        </select>
      </div>
      <div class="dev-plan">
        <p class="title-sm-block">開発:</p>
        <select class="plan-select-develop" v-model="selectedPlan">
          <option v-for="plan of store.planCandidate" :key="plan.key" :value="plan.key">
            {{ plan.data.name }} {{ plan.data.priceString }}
          </option>
          <option v-if="store.planCandidate.find((p) => p.key === 'grading')" value="all_grading">
            全荒地に整地入力 (荒地x5億円)
          </option>
          <option v-if="store.planCandidate.find((p) => p.key === 'ground_leveling')" value="all_ground_leveling">
            全荒地に高速整地入力 (荒地x100億円)
          </option>
          <option v-if="store.planCandidate.find((p) => p.key === 'excavation')" value="all_excavation">
            全浅瀬に掘削入力 (浅瀬x200億円)
          </option>
        </select>
      </div>
      <div class="dev-plan">
        <p class="title-sm-block">座標:</p>
        <div class="plan-select-pos">
          <span>( </span>
          <span>
            <select v-model="store.selectedPoint.x">
              <option v-for="x of store.hakoniwa.height" :key="x" :value="x - 1">
                {{ x - 1 }}
              </option>
            </select>
          </span>
          <span> , </span>
          <span>
            <select v-model="store.selectedPoint.y">
              <option v-for="y of store.hakoniwa.height" :key="y" :value="y - 1">
                {{ y - 1 }}
              </option>
            </select>
          </span>
          <span> )</span>
        </div>
      </div>
      <div class="dev-plan">
        <p class="title-sm-block">数量</p>
        <select class="float-right" v-model="store.selectedAmount">
          <option v-for="n of 100" :key="n - 1" :value="n - 1">
            {{ n - 1 }}
          </option>
        </select>
      </div>
      <div class="dev-plan pt-2">
        <p class="title-block">目標の島:</p>
        <div class="plan-target-island">
          <select class="target-select" v-model="store.selectedTargetIsland">
            <option v-for="targetIsland of store.targetIslands" :key="targetIsland.id" :value="targetIsland.id">
              {{ targetIsland.name }} 島
            </option>
          </select>
          <button class="target-open button-surface dark:button-variant-reverse" @click="openIslandPopup()">
            <template v-if="store.isOpenPopup"> 閉じる</template>
            <template v-else> 開く</template>
          </button>
        </div>
      </div>
    </div>

    <div class="mb-4">
      <div class="section-header">操作</div>
      <div class="mb-2 px-2 text-left">
        <p class="title-sm-block">コマンド移動</p>
        <div class="move-command">
          <button
            class="move-command-button dark:button-variant-reverse mr-2"
            @click="onClickMoveUp()"
            :disabled="store.selectedPlanNumber <= 1">
            ▲
          </button>
          <button
            class="move-command-button dark:button-variant-reverse"
            @click="onClickMoveDown()"
            :disabled="store.selectedPlanNumber >= MAX_PLAN_NUMBER">
            ▼
          </button>
        </div>
      </div>
      <div class="send-plan">
        <button class="send-plan-button" :class="{ 'button-disabled': store.isSendingPlan }" @click="onClickSendPlan()">
          <span v-if="!store.isSendingPlan">計画送信</span>
          <span v-if="store.isSendingPlan" class="button-circle">
            <span class="button-circle-spin"></span>
          </span>
        </button>
      </div>
    </div>
    <PlansNotification></PlansNotification>
  </div>
</template>

<script setup lang="ts">
import { Plan } from '$js/entity/Plan'
import PlansNotification from './PlansNotification.vue'
import { ref } from 'vue'
import { Point } from '$js/entity/Point'
import { useIslandEditorStore } from '$store/IslandEditorStore.js'

const MAX_PLAN_NUMBER = 30
const selectedPlan = ref('grading')

const store = useIslandEditorStore()

const getSelectedPlan = (): Plan => {
  const result = store.planCandidate.find((c) => c.key === selectedPlan.value)
  if (result === undefined) return null
  else {
    const p = result.data
    return {
      key: selectedPlan.value,
      data: {
        name: p.name,
        point: {
          x: store.selectedPoint.x,
          y: store.selectedPoint.y
        },
        amount: store.selectedAmount,
        usePoint: p.usePoint,
        useAmount: p.useAmount,
        useTargetIsland: p.useTargetIsland,
        targetIsland: store.selectedTargetIsland,
        isFiring: p.isFiring,
        priceString: p.priceString,
        amountString: p.amountString,
        defaultAmountString: p.defaultAmountString
      }
    }
  }
}

const getCustomPlan = (key: string, point: Point): Plan => {
  const result = store.planCandidate.find((c) => c.key === key)
  if (result === undefined) return null
  else {
    return {
      key: key,
      data: {
        name: result.data.name,
        point: {
          x: point.x,
          y: point.y
        },
        amount: store.selectedAmount,
        usePoint: result.data.usePoint,
        useAmount: result.data.useAmount,
        useTargetIsland: result.data.useTargetIsland,
        targetIsland: store.selectedTargetIsland,
        isFiring: result.data.isFiring,
        priceString: result.data.priceString,
        amountString: result.data.amountString,
        defaultAmountString: result.data.defaultAmountString
      }
    }
  }
}

const insertPlanAutomatically = (source: string, target: string) => {
  // targetのコマンドが操作できない場合はreturn
  if (!store.planCandidate.find((p) => p.key === target)) {
    return
  }
  for (let terrain of store.terrains) {
    if (terrain.type === source) {
      let plan = getCustomPlan(target, terrain.data.point)

      store.plans.splice(store.selectedPlanNumber - 1, 0, plan)
      store.plans.pop()
      if (store.selectedPlanNumber < MAX_PLAN_NUMBER) {
        store.selectedPlanNumber++
      }
    }
  }
}

const overwritePlanAutomatically = (source: string, target: string) => {
  // targetのコマンドが操作できない場合はreturn
  if (!store.planCandidate.find((p) => p.key === target)) {
    return
  }
  for (let terrain of store.terrains) {
    if (terrain.type === source) {
      store.plans[store.selectedPlanNumber - 1] = getCustomPlan(target, terrain.data.point)

      if (store.selectedPlanNumber < MAX_PLAN_NUMBER) {
        store.selectedPlanNumber++
      }
    }
  }
}

const onClickInsert = () => {
  if (selectedPlan.value === 'all_grading') {
    insertPlanAutomatically('wasteland', 'grading')
    return
  } else if (selectedPlan.value === 'all_ground_leveling') {
    insertPlanAutomatically('wasteland', 'ground_leveling')
    return
  } else if (selectedPlan.value === 'all_excavation') {
    insertPlanAutomatically('shallow', 'excavation')
    return
  }

  store.plans.splice(store.selectedPlanNumber - 1, 0, getSelectedPlan())
  store.plans.pop()
  if (store.selectedPlanNumber < MAX_PLAN_NUMBER) {
    store.selectedPlanNumber++
  }
}
const onClickOverwrite = () => {
  if (selectedPlan.value === 'all_grading') {
    overwritePlanAutomatically('wasteland', 'grading')
    return
  } else if (selectedPlan.value === 'all_ground_leveling') {
    overwritePlanAutomatically('wasteland', 'ground_leveling')
    return
  } else if (selectedPlan.value === 'all_excavation') {
    overwritePlanAutomatically('shallow', 'excavation')
    return
  }

  store.plans[store.selectedPlanNumber - 1] = getSelectedPlan()

  if (store.selectedPlanNumber < MAX_PLAN_NUMBER) {
    store.selectedPlanNumber++
  }
}

const onClickDelete = () => {
  store.plans.splice(store.selectedPlanNumber - 1, 1)
  store.plans.push(store.getDefaultPlan)
}

const onClickMoveUp = () => {
  if (store.selectedPlanNumber <= 1) {
    return
  }
  ;[store.plans[store.selectedPlanNumber - 2], store.plans[store.selectedPlanNumber - 1]] = [
    store.plans[store.selectedPlanNumber - 1],
    store.plans[store.selectedPlanNumber - 2]
  ]
  store.selectedPlanNumber--
}

const onClickMoveDown = () => {
  if (store.selectedPlanNumber >= 30) {
    return
  }
  ;[store.plans[store.selectedPlanNumber], store.plans[store.selectedPlanNumber - 1]] = [
    store.plans[store.selectedPlanNumber - 1],
    store.plans[store.selectedPlanNumber]
  ]
  if (store.selectedPlanNumber < MAX_PLAN_NUMBER) {
    store.selectedPlanNumber++
  }
}

const onClickSendPlan = () => {
  store.isSendingPlan = true
  store.putPlan()
}

const openIslandPopup = () => {
  store.getIslandTerrain(store.selectedTargetIsland)
  store.isOpenPopup = true
  store.showPlanWindow = false
}
</script>

<style lang="scss" scoped>
#plan-controller {
  @apply mx-1 mb-3 rounded-xl bg-surface-variant p-1 text-on-surface-variant drop-shadow-md max-lg:h-fit md:max-w-[200px] lg:mr-2 lg:max-w-[230px] lg:p-2;

  &.order-2 {
    @apply w-[45%] max-w-full;
  }

  .section-header {
    @apply mb-2 w-full border-b border-on-surface-variant pl-2 text-left text-sm text-on-surface-variant;
  }

  .action {
    @apply grid grid-cols-3 gap-2 px-1 lg:px-2;

    .action-button {
      @apply p-1 max-lg:text-xs lg:text-sm;
    }
  }

  .title-sm-inline {
    @apply mr-2 inline-block max-lg:text-sm;
  }

  .title-sm-block {
    @apply mr-2 inline-block max-lg:mb-0 max-lg:text-sm;
  }

  .title-block {
    @apply mb-1 block w-full max-lg:mb-0 max-lg:text-sm;
  }

  .dev-plan {
    @apply mb-2 px-2 text-left lg:mb-3;

    .plan-select-develop {
      @apply w-full text-sm lg:float-right lg:w-3/4;
    }

    .plan-select-pos {
      @apply inline-block max-lg:w-full max-lg:text-center lg:float-right;
    }

    .plan-target-island {
      @apply flex w-full items-center max-lg:flex-wrap max-lg:justify-end;

      .target-select {
        @apply text-sm max-lg:w-full md:max-w-[80%] lg:float-right lg:mr-2 lg:grow;
      }

      .target-open {
        @apply px-1 py-0.5 text-xs max-lg:mt-1;
      }
    }
  }

  .move-command {
    @apply text-center max-lg:w-full lg:float-right;

    .move-command-button {
      @apply px-1.5 py-0.5 text-sm;
    }
  }

  .send-plan {
    @apply mt-8 px-2 lg:mt-4;

    .send-plan-button {
      @apply button-primary w-2/3 py-1 lg:w-1/2;
    }

    .button-circle {
      @apply block flex justify-center;

      .button-circle-spin {
        @apply m-1 block h-4 w-4 animate-spin rounded-full border-2 border-primary border-t-transparent;
      }
    }
  }
}
</style>
