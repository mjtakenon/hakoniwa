<template>
  <div id="sightseeing-page" class="wrapper">
    <StatusTable :island="store.island" :status="store.status" :achievements="store.achievements" />
    <IslandViewer />
    <div class="md:max-lg:px-3">
      <Bbs :island="store.island" />
      <LogViewer :title="store.island.name + '島の近況'" :parsed-logs="store.logs" />
    </div>
  </div>
</template>

<script setup lang="ts">
import StatusTable from '../../islands/common/StatusTable.vue'
import LogViewer from '../../islands/common/LogViewer.vue'
import IslandViewer from './IslandsViewer.vue'
import { ref } from 'vue'
import { Hakoniwa } from '../../../store/Entity/Hakoniwa'
import { Status } from '../../../store/Entity/Status'
import { Terrain } from '../../../store/Entity/Terrain'
import { Plan } from '../../../store/Entity/Plan'
import { LogParser, LogProps, SummaryProps } from '../../../store/Entity/Log'
import { AchievementProp } from '../../../store/Entity/Achievement'
import { BbsMessage } from '../../../store/Entity/Bbs'
import Bbs from '../../islands/common/Bbs.vue'
import { useIslandViewerStore } from '../../../store/IslandViewerStore.js'
import { useBbsStore } from '../../../store/BbsStore.js'

let hoverWindowTop = ref(170)
let hoverWindowLeft = ref(0)

const store = useIslandViewerStore()

interface Props {
  hakoniwa: Hakoniwa
  // TODO: ここで飛んでくるislandはPlansController.phpで定義されており、js/store/Entity/Islandの中身と異なっている　共通化できないか？
  island: {
    id: number
    name: string
    owner_name: string
    status: Status
    terrains: Array<Terrain>
    plans: Array<Plan>
    logs: LogProps[]
    summary: SummaryProps[]
    comment?: string
    achievements: AchievementProp[]
    bbs: BbsMessage[]
  }
}

const props = defineProps<Props>()

// Logsのparse
const parser = new LogParser()
const logs = parser.parse(props.island.logs, props.island.summary)

store.$patch((state) => {
  state.hakoniwa = props.hakoniwa
  state.island = {
    id: props.island.id,
    name: props.island.name,
    owner_name: props.island.owner_name,
    comment: props.island.comment,
    terrains: props.island.terrains
  }
  state.status = props.island.status
  state.terrains = props.island.terrains
  state.logs = logs
  state.achievements = props.island.achievements
})

useBbsStore().bbs = props.island.bbs
</script>

<style lang="scss" scoped>
#sightseeing-page {
  @apply mx-auto min-h-[1200px] max-w-[1000px] text-center;
}
</style>
