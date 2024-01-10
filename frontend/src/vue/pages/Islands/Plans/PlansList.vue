<template>
  <div id="plan-list" :class="[isPlanSent ? 'bg-surface-variant' : 'bg-error-container text-on-error-container']">
    <div class="send-status">
      <div v-if="isPlanSent" class="send-status-text bg-background text-on-background">-- 計画送信済み --</div>
      <div v-else class="send-status-text bg-error text-on-error">-- 計画未送信 --</div>
    </div>
    <div class="plans">
      <div
        v-for="(plan, index) of store.plans"
        :key="'list-' + index + '-' + plan.key"
        class="plan"
        @click="onClickPlan(index)"
        :class="{ 'bg-white/30': index + 1 === store.selectedPlanNumber }">
        <!--                :style="[ parseInt(index)+1 === store.selectedPlanNumber ? { textDecoration: 'underline' } : '']"-->
        <span class="plan-index">
          {{ index + 1 }}
        </span>
        <span class="plan-separator">:</span>
        <div class="plan-desc">
          <span
            v-if="
              plan.data.useTargetIsland &&
              plan.data.targetIsland !== null &&
              store.targetIslands.filter((i) => {
                return i.id === plan.data.targetIsland
              }).length >= 1
            ">
            {{
              store.targetIslands.filter((i) => {
                return i.id === plan.data.targetIsland
              })[0].name
            }}島 &#x20;
          </span>
          <span v-if="plan.data.usePoint"> 地点 ({{ plan.data.point.x }},{{ plan.data.point.y }}) </span>
          <span
            v-if="
              plan.data.usePoint ||
              (plan.data.useTargetIsland &&
                plan.data.targetIsland !== null &&
                store.targetIslands.filter((i) => {
                  return i.id === plan.data.targetIsland
                }).length >= 1)
            ">
            <span v-if="plan.data.useTargetIsland">へ</span>
            <span v-else>に</span>
          </span>
          <span class="font-bold">
            {{ plan.data.name }}
          </span>
          <span v-if="plan.data.useAmount">
            <span v-if="plan.data.amount === 0"> {{ plan.data.defaultAmountString }}</span>
            <span v-else> {{ plan.data.amountString.replace(':amount:', plan.data.amount.toString()) }} </span>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useIslandEditorStore } from '$store/IslandEditorStore.js'

const store = useIslandEditorStore()

const onClickPlan = (index: number) => {
  store.selectedPlanNumber = index + 1
}

const isPlanSent = computed(() => {
  return JSON.stringify(store.plans) === JSON.stringify(store.sentPlans)
})
</script>

<style lang="scss" scoped>
#plan-list {
  @apply mx-1 mb-3 max-w-[200px] overflow-y-hidden overflow-x-visible rounded-xl p-2 text-left drop-shadow-md md:max-h-[496px] lg:ml-3 lg:max-w-[230px];
  // 2-column
  &.order-2 {
    @apply max-h-[65vh] w-[45%] max-w-full;
  }

  // 3-column

  .send-status {
    @apply mb-2 w-full text-center text-white;

    .send-status-text {
      @apply w-full rounded-2xl py-1;
    }
  }

  .plans {
    @apply h-[90%] w-full overflow-y-scroll text-sm;

    .plan {
      @apply flex w-full;

      .plan-index {
        @apply mr-0.5 min-w-[1rem];
      }

      .plan-separator {
        @apply mr-0.5 min-w-[3%];
      }

      .plan-desc {
        @apply inline-block grow;
      }
    }
  }
}
</style>
