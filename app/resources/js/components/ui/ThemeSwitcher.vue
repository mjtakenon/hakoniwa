<template>
  <div id="theme-switch">
    <div class="switch-bar" @click="onClickThemeToggle">
      <div class="switch-button" :class="{ active: isDark }"></div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { Theme } from '../../store/Entity/Theme'
import { useMainStore } from '../../store/MainStore'

export default defineComponent({
  data() {
    return {
      themes: [
        { name: 'light', themeClass: 'theme-light', type: 'light' },
        { name: 'dark', themeClass: 'theme-dark', type: 'dark' }
      ] as Theme[]
    }
  },
  setup() {
    const store = useMainStore()
    return { store }
  },
  watch: {
    theme() {
      console.debug('changed')
    }
  },
  computed: {
    isDark() {
      return this.store.theme.type === 'dark'
    }
  },
  methods: {
    onClickThemeToggle() {
      if (this.isDark) {
        this.store.changeTheme(this.themes[0])
      } else {
        this.store.changeTheme(this.themes[1])
      }
    }
  }
})
</script>

<style scoped lang="scss">
#theme-switch {
  .switch-bar {
    @apply relative my-2 h-5 w-10 cursor-pointer rounded-3xl;
    @apply bg-surface-variant;
    @apply transition-all duration-300 ease-in-out;
    // sp
    @apply mx-auto;
    // desktop
    @apply md:mx-2;

    .switch-button {
      @apply absolute bottom-0 left-0 top-0 m-auto h-5 w-5 rounded-3xl;
      @apply bg-on-surface-variant;
    }
    .switch-button.active {
      @apply left-5;
    }
  }
}
</style>
