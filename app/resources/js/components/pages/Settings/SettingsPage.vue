<template>
  <div id="settings">
    <div class="settings-contents">
      <h1 class="title">ユーザ設定</h1>
      <SettingsInfo :change_island_name_price="change_island_name_price" />
      <SettingsTheme />
    </div>
  </div>
</template>

<script setup lang="ts">
import { Status } from '../../../store/Entity/Status.js'
import { useMainStore } from '../../../store/MainStore.js'
import SettingsInfo from './SettingsInfo.vue'
import SettingsTheme from './SettingsTheme.vue'

interface Props {
  island: {
    id: number
    name: string
    owner_name: string
    status: Status
  }
  change_island_name_price: number
}
const props = defineProps<Props>()

const store = useMainStore()

store.$patch((state) => {
  state.island = {
    id: props.island.id,
    name: props.island.name,
    owner_name: props.island.owner_name
  }
  state.status = props.island.status
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
