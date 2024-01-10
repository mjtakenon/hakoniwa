<template>
  <div id="theme-switch">
    <div class="switch-bar" @click="onClickThemeToggle()">
      <div class="switch-button" :class="{ active: isDark }"></div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Theme } from '$entity/Theme'
import { useUserSettingsStore } from '$store/UserSettingsStore.js'

const userSettings = useUserSettingsStore()

const themes: Theme[] = [
  { name: 'light', themeClass: 'theme-light', type: 'light' },
  { name: 'dark', themeClass: 'theme-dark', type: 'dark' }
]

const isDark = computed(() => {
  return userSettings.theme.type === 'dark'
})

const onClickThemeToggle = () => {
  if (isDark.value) {
    userSettings.changeTheme(themes[0])
  } else {
    userSettings.changeTheme(themes[1])
  }
}
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
