<template>
  <div id="sightseeing-page" class="wrapper">
    <StatusTable :island="islandViewerStore.island" :status="islandViewerStore.status"
                 :achievements="islandViewerStore.achievements"/>
    <div id="island">
      <IslandViewerCanvas/>
      <IslandHoverWindow
        :showHoverWindow="islandViewerStore.showHoverWindow"
        :hoverWindowPoint="islandViewerStore.hoverWindowPoint"
        :hoverCellPoint="islandViewerStore.hoverCellPoint"
        :terrain="islandViewerStore.terrain"/>
    </div>
    <div class="md:max-lg:px-3">
      <Bbs :island="islandViewerStore.island"/>
      <LogViewer :title="islandViewerStore.island.name + '島の近況'" :parsed-logs="islandViewerStore.logs"/>
    </div>
  </div>
</template>

<script setup lang="ts">
import StatusTable from '$vue/components/islands/common/StatusTable.vue'
import LogViewer from '$vue/components/islands/common/LogViewer.vue'
import {Hakoniwa} from '$entity/Hakoniwa'
import {Status} from '$entity/Status'
import {Plan} from '$entity/Plan'
import {LogParser, LogProps, SummaryProps} from '$entity/Log'
import {AchievementProp, getAchievementsList} from '$entity/Achievement'
import {BbsMessage} from '$entity/Bbs'
import Bbs from '$vue/components/islands/common/Bbs.vue'
import {useIslandViewerStore} from '$store/IslandViewerStore.js'
import {useBbsStore} from '$store/BbsStore.js'
import IslandViewerCanvas from '$vue/components/islands/Viewer/IslandViewerCanvas.vue'
import IslandHoverWindow from '$vue/components/islands/Hover/IslandHoverWindow.vue'
import {Cell} from "$entity/Cell.js";
import {Edge} from "$entity/Edge.js";

const islandViewerStore = useIslandViewerStore()

interface Props {
  hakoniwa: Hakoniwa
  // TODO: ここで飛んでくるislandはPlansController.phpで定義されており、js/entity/Islandの中身と異なっている　共通化できないか？
  island: {
    id: number
    name: string
    owner_name: string
    status: Status
    terrain: {
      cells: Cell[]
      edges: Edge[]
    }
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

islandViewerStore.$patch((state) => {
  state.hakoniwa = props.hakoniwa
  state.island = {
    id: props.island.id,
    name: props.island.name,
    owner_name: props.island.owner_name,
    comment: props.island.comment,
    terrain: {
      cells: props.island.terrain.cells,
      edges: props.island.terrain.edges
    }
  }
  state.status = props.island.status
  state.terrain = {
    cells: props.island.terrain.cells,
    edges: props.island.terrain.edges
  }
  state.logs = logs
  state.achievements = getAchievementsList(props.island.achievements)
})

useBbsStore().bbs = props.island.bbs
</script>

<style lang="scss" scoped>
#sightseeing-page {
  @apply mx-auto min-h-[1200px] max-w-[1000px] text-center;
}

#island {
  margin: 0 auto;
  @apply mb-4 w-full max-w-[512px] md:min-w-[512px];

  #island-canvas {
    @apply mb-4 max-h-[512px] min-h-[512px] w-full;
  }

  .row {
    @apply m-0 bg-black p-0;
    display: grid;

    .cell {
      @apply aspect-square w-full;
    }

    &:nth-child(odd) {
      grid-template-columns: 1fr repeat(15, 2fr);
    }

    &:nth-child(even) {
      grid-template-columns: repeat(15, 2fr) 1fr;
    }

    .left-padding {
      @apply z-10 aspect-[1/2] w-full;
      background-image: url('/img/hakoniwa/hakogif/land0.gif');
      background-position: left;
    }

    .right-padding {
      @apply relative z-10 aspect-[1/2] w-full;
      background-image: url('/img/hakoniwa/hakogif/land0.gif');
      background-position: right;

      .right-padding-text {
        @apply absolute left-1 z-10 overflow-hidden text-xs leading-none text-white max-xs:hidden md:text-sm;
      }
    }
  }
}
</style>
