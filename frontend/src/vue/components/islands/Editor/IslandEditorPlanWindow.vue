<template>
  <div
    v-show="store.showPlanWindow"
    class="plan-window"
    :style="[{ top: store.planWindow.y + 'px' }, { left: store.planWindow.x + 'px' }]">
    <div class="plan-window-header">
      <div class="grow px-3">
        <span class="mr-2">({{ store.selectedPoint.x }},{{ store.selectedPoint.y }})</span>
        <span class="text-xs">計画番号: </span>
        <span class="mr-1">{{ store.selectedPlanNumber }}</span>
      </div>

      <button class="plan-window-close" @click="onClickClosePlan">×</button>
    </div>
    <div v-for="plan of planCandidate" :key="plan.key" class="plan-window-select">
      <div @click="onClickPlan(plan.key)">
        <a class="action-name">{{ plan.data.name }}</a>
        <span class="action-price">{{ plan.data.priceString }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Plan } from '$entity/Plan'
import { computed } from 'vue'
import { useIslandEditorStore } from '$store/IslandEditorStore.js'

const store = useIslandEditorStore()

const MAX_PLAN_NUMBER = 30

const planCandidate = computed(() => {
  return store.planCandidate.filter((p) =>
    store.isOpenPopup ? p.data.usePoint && p.data.useTargetIsland : p.data.usePoint
  )
})

const onClickClosePlan = () => {
  store.showPlanWindow = false
}

const onClickPlan = (key) => {
  store.plans.splice(store.selectedPlanNumber - 1, 0, getSelectedPlan(key))
  store.plans.pop()
  if (store.selectedPlanNumber < MAX_PLAN_NUMBER) {
    store.selectedPlanNumber++
  }
  store.showPlanWindow = false
}

const getSelectedPlan = (key): Plan => {
  const result = store.planCandidate.find((c) => c.key === key)
  if (result === undefined) return null
  else {
    const p = result.data
    return {
      key: key,
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
</script>

<style lang="scss" scoped>
.plan-window {
  @apply absolute block;
  @apply z-30 w-fit overflow-hidden rounded-md border border-primary bg-surface-variant text-left text-on-surface-variant drop-shadow-xl dark:border-primary-container max-lg:min-w-[230px] max-lg:max-w-[230px] max-lg:-translate-x-1/2 max-md:text-sm lg:max-w-[240px];
  @apply animate-fadein;

  .plan-window-header {
    @apply m-0 flex items-center bg-primary p-0 text-on-primary dark:bg-primary-container dark:text-on-primary-container;

    .plan-window-close {
      @apply mr-3 inline-block border-none bg-primary p-0 text-on-primary drop-shadow-none hover:bg-primary dark:bg-primary-container dark:text-on-primary-container hover:dark:bg-primary-container;
    }
  }

  .plan-window-select {
    @apply w-full px-2 hover:bg-on-primary max-md:py-1;

    .action-name {
      @apply mr-1 inline-block text-sm font-bold md:text-base;
    }

    .action-price {
      @apply inline-block text-xs;
    }

    &:not(:last-child) {
      @apply border-b border-gray-700;
    }
  }
}
</style>
