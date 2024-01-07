<template>
  <div
    class="fixed bottom-0 z-[100] flex h-fit w-full items-center bg-alert py-1 text-on-alert"
    :class="{ hidden: !visible }">
    <div class="mr-5 text-xs">DEBUG TOOLS:</div>
    <div class="text-xs">theme:</div>
    <ThemeSwitcher></ThemeSwitcher>
    <a v-if="debugLoginUsingId >= 1" class="button-primary login" href="/auth/debug/login">
      <div>ログイン</div>
    </a>
    <div class="ml-auto text-right text-xs">close: PAUSE key</div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import ThemeSwitcher from '../ui/ThemeSwitcher.vue'

interface Props {
  debugLoginUsingId?: number
}

const props = withDefaults(defineProps<Props>(), {
  debugLoginUsingId: 0
})

let visible = ref(true)

onMounted(() => {
  document.addEventListener('keydown', onKeyDown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', onKeyDown)
})

const onKeyDown = (event: KeyboardEvent) => {
  if (event.key === 'Pause') {
    visible.value = !visible.value
  }
}
</script>

<style scoped lang="scss">
.login {
  @apply ml-1 p-1 text-sm;
}
</style>
