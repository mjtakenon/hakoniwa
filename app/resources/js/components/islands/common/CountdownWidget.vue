<template>
  <div id="countdown-widget">
    <div class="countdown-box rounded-br-xl md:rounded-b-xl">
      <span class="countdown-label mr-1">ターン:</span>
      <span class="text-sm">{{ store.turn.turn }}</span>
    </div>
    <div class="countdown-box rounded-bl-xl md:rounded-b-xl" :class="{ 'countdown-box-updated': !isTimeRemaining }">
      <template v-if="isTimeRemaining">
        <span class="countdown-label mr-1">次の更新まで</span>
        <template v-if="remainTimes.hour > 0">
          <span class="text-sm">{{ remainTimes.hour }}</span>
          <span class="countdown-unit">時間</span>
        </template>
        <template v-if="remainTimes.hour > 0 || remainTimes.min > 0">
          <span class="text-sm">{{ remainTimes.min < 10 ? '0' + remainTimes.min : remainTimes.min.toString() }}</span>
          <span class="countdown-unit">分</span>
        </template>
        <span class="text-sm">{{ remainTimes.sec < 10 ? '0' + remainTimes.sec : remainTimes.sec.toString() }}</span>
        <span class="countdown-unit">秒</span>
      </template>
      <template v-else>
        <span class="text-sm">更新済み</span>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import { useIslandEditorStore } from '../../../store/IslandEditorStore.js'

const remainTimes = ref<{ hour: number; min: number; sec: number }>({ hour: 0, min: 0, sec: 0 })
const isTimeRemaining = ref(true)
const secInterval = ref<NodeJS.Timeout>()

const store = useIslandEditorStore()

onMounted(() => {
  secInterval.value = setInterval(countDownTurn, 1000)
})

onUnmounted(() => {
  clearInterval(secInterval.value)
})

const countDownTurn = () => {
  const now = new Date()
  if (now > store.turn.next_time) {
    isTimeRemaining.value = false
    clearInterval(secInterval.value)
  } else {
    const diff = store.turn.next_time.getTime() - now.getTime()
    const h = Math.floor(diff / 1000 / 60 / 60)
    const m = Math.floor(diff / 1000 / 60) % 60
    const s = Math.floor(diff / 1000) % 60
    remainTimes.value = {
      hour: h,
      min: m,
      sec: s
    }
  }
}
</script>

<style scoped lang="scss">
#countdown-widget {
  @apply mb-4 flex h-6 w-full justify-between drop-shadow lg:mb-2;

  .countdown-box {
    @apply inline-flex items-center bg-surface-variant px-2;
  }

  .countdown-box-updated {
    @apply bg-error text-on-error dark:bg-error-container dark:text-on-error-container;
  }

  .countdown-label {
    @apply text-xs text-on-surface-variant;
  }

  .countdown-unit {
    @apply mb-0.5 mr-1 mt-auto text-[0.5rem] text-on-surface-variant;
  }

  .countdown-unit:last-child {
    @apply mr-0;
  }
}
</style>
