<template>
  <div id="settings">
    <div class="settings-contents">
      <h1 class="title">ユーザ設定</h1>
      <SettingsInfo :changeIslandNamePrice="changeIslandNamePrice" />
      <SettingsTheme />
    </div>
  </div>
</template>

<script setup lang="ts">
import { Status } from '$js/entity/Status.js'
import SettingsInfo from './SettingsInfo.vue'
import SettingsTheme from './SettingsTheme.vue'
import { useUserStore } from '$store/UserStore.js'

interface Props {
  island: {
    id: number
    name: string
    owner_name: string
    status: Status
  }
  changeIslandNamePrice: number
}
const props = defineProps<Props>()

const store = useUserStore()
store.$patch((state) => {
  state.user.status = props.island.status
})
</script>

<style scoped lang="scss">
#settings {
  @apply min-h-[100vh] w-full;

  .settings-contents {
    @apply mx-auto max-w-3xl;

    .title {
      @apply mb-10 border-b p-2 pb-4;
    }
  }
}
</style>
