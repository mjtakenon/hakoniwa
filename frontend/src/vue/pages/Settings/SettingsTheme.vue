<template>
  <div class="theme-settings">
    <h2 class="subtitle">テーマ設定</h2>
    <p class="descriptions">画面表示のテーマを設定できます。</p>
    <div class="theme-buttons">
      <div v-for="theme of themes" class="mr-2" :class="theme.themeClass">
        <button @click="onClickTheme(theme)">{{ theme.name }}</button>
      </div>
    </div>
    <div class="theme-samples">
      <h3 class="samples-title">サンプル</h3>
      <div class="samples-wrapper">
        <div class="samples-grid">
          <div class="bg-background text-on-background">background</div>
          <div class="bg-surface text-on-surface">surface</div>
          <div class="bg-primary text-on-primary">
            <div>primary</div>
            <div class="bg-primary-container text-on-primary-container">primary container</div>
          </div>
          <div class="bg-secondary text-on-secondary">
            <div>secondary</div>
            <div class="bg-secondary-container text-on-secondary-container">secondary container</div>
          </div>
          <div class="bg-alert text-on-alert">
            <div>alert</div>
            <div class="bg-alert-container text-on-alert-container">alert container</div>
          </div>
          <div class="bg-error text-on-error">
            <div>error</div>
            <div class="bg-error-container text-on-error-container">error container</div>
          </div>
          <div class="bg-surface-variant text-on-surface-variant">surface-variant</div>
          <div class="border-2">outline</div>
          <div class="font-bold text-on-link">Link Text Color</div>
          <div class="font-bold">
            <div>
              <span class="mr-3">PLUS</span>
              <span class="border-b-2 border-b-plus text-on-plus">+ 100 pts</span>
            </div>
            <div>
              <span class="mr-3">MINUS</span>
              <span class="border-b-2 border-b-minus text-on-minus">- 100 pts</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import themeListJson from '$js/ThemeList.json'
import { Theme } from '$js/entity//Theme.js'
import { useUserSettingsStore } from '$store/UserSettingsStore.js'

const themes = ref<Theme[]>(themeListJson as Theme[])
const userSettings = useUserSettingsStore()

const onClickTheme = (theme: Theme) => {
  if (userSettings.theme.name === theme.name) return
  userSettings.changeTheme(theme)
}
</script>

<style scoped lang="scss">
.theme-settings {
  // general
  @apply w-full rounded-md bg-surface-variant;
  // sp
  @apply p-2;
  // desktop
  @apply md:px-6 md:py-4;

  .subtitle {
    @apply my-2 border-l-4 border-on-background pl-6 font-bold;
  }

  .descriptions {
    @apply mx-2 mt-4 md:mx-6;
  }

  .theme-buttons {
    @apply flex justify-center;
  }

  .theme-samples {
    @apply mt-6;
    // sp
    @apply px-2;
    // desktop
    @apply md:px-10;

    .samples-title {
      @apply mb-4 border-b border-dashed border-on-surface-variant text-on-surface-variant;
    }

    .samples-wrapper {
      @apply w-full bg-background p-2;

      .samples-grid {
        @apply grid gap-2;
        // sp
        @apply grid-cols-1;
        // desktop
        @apply md:grid-cols-2;
      }

      div {
        @apply flex min-h-[4rem] w-full flex-wrap items-center justify-center rounded-md p-3;
      }
    }
  }
}
</style>
