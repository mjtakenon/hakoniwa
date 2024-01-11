<template>
  <div class="grid grid-cols-2 gap-4">
    <div class="turn-box">
      <h2 class="turn-box-title">ターン</h2>
      <p class="turn-box-text-big m-0 h-2/3">{{ props.turn }}</p>
    </div>
    <div class="turn-box">
      <h2 class="turn-box-title">次回更新予定</h2>
      <p class="turn-box-text-big max-md:hidden">{{ props.day + ' ' + props.time }}</p>
      <p class="turn-box-text-medium mb-0 md:hidden">{{ props.day }}</p>
      <p class="turn-box-text-medium -mt-1 md:hidden">{{ props.time }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
type Props = {
  turn: string
  day: string
  time: string
}

const props = defineProps<Props>()
</script>

<style lang="scss" scoped>
.turn-box {
  @apply rounded bg-primary p-1 text-on-primary drop-shadow-md;

  .turn-box-title {
    @apply font-bold;
  }

  .turn-box-text-big,
  .turn-box-text-medium {
    @apply text-center font-black;
  }

  .turn-box-text-big {
    @apply text-3xl;
  }

  .turn-box-text-medium {
    @apply text-lg;
  }
}
</style>
