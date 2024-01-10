import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Theme } from '$js/entity/Theme.js'

export const useUserSettingsStore = defineStore('user-settings', () => {
  const theme = ref<Theme>({
    name: 'light',
    themeClass: 'theme-light',
    type: 'light'
  })

  function changeTheme(newTheme: Theme) {
    const app = document.getElementById('app')
    theme.value = newTheme
    app.classList.remove(...app.classList)
    app.classList.add(newTheme.themeClass)
    app.classList.add(newTheme.type.toString())
    localStorage.setItem('theme', JSON.stringify(newTheme))
  }

  return { theme, changeTheme }
})
