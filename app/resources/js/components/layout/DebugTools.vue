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

<script lang="ts">
import { defineComponent } from 'vue'
import ThemeSwitcher from '../ui/ThemeSwitcher.vue'

export default defineComponent({
  components: { ThemeSwitcher },
  data() {
    return {
      visible: true
    }
  },
  mounted() {
    document.addEventListener('keydown', this.onKeyDown)
  },
  unmounted() {
    document.removeEventListener('keydown', this.onKeyDown)
  },
  methods: {
    onKeyDown(event: KeyboardEvent) {
      if (event.key === 'Pause') {
        this.visible = !this.visible
      }
    }
  },
  props: {
    debugLoginUsingId: {
      require: false,
      type: Number,
      default: 0
    }
  }
})
</script>

<style scoped lang="scss">
.login {
  @apply ml-1 p-1 text-sm;
}
</style>
