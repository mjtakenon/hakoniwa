<template>
  <div class="ranking" :id="'ranking-' + props.island.id" :class="[isAppeared ? 'active' : '']">
    <div class="ranking-index">
      <div class="ranking-index-num">{{ props.index }}</div>
      <a class="ranking-index-info block" :href="'/islands/' + props.island.id">
        <div v-if="props.island.abandoned_turn >= 1" class="ranking-index-name" :class="islandNameSize">
          {{ props.island.name }}島 ({{ props.island.abandoned_turn }})
        </div>
        <div v-else class="ranking-index-name" :class="islandNameSize">{{ props.island.name }}島</div>
        <div class="ranking-index-owner">
          {{ props.island.owner_name }}
        </div>
      </a>
    </div>
    <div class="ranking-data">
      <div class="ranking-summary">
        <div class="ranking-summary-wrapper">
          <div class="ranking-summary-title">発展ポイント</div>
          <div class="ranking-summary-data">
            <div class="ranking-summary-data-num">{{ props.island.development_points }}</div>
            <div class="ranking-summary-data-unit">pts</div>
          </div>
        </div>
        <div class="ranking-summary-wrapper">
          <div class="ranking-summary-title">人口</div>
          <div class="ranking-summary-data">
            <div class="ranking-summary-data-num">{{ props.island.population }}</div>
            <div class="ranking-summary-data-unit">人</div>
          </div>
        </div>
        <div class="ranking-summary-wrapper">
          <div class="ranking-summary-title">資金</div>
          <div class="ranking-summary-data">
            <div class="ranking-summary-data-num">{{ props.island.funds }}</div>
            <div class="ranking-summary-data-unit">億円</div>
          </div>
        </div>
        <div class="ranking-summary-wrapper">
          <div class="ranking-summary-title">食料</div>
          <div class="ranking-summary-data">
            <div class="ranking-summary-data-num">{{ props.island.foods }}</div>
            <div class="ranking-summary-data-unit">㌧</div>
          </div>
        </div>
        <div class="ranking-summary-wrapper">
          <div class="ranking-summary-title">資源</div>
          <div class="ranking-summary-data">
            <div class="ranking-summary-data-num">{{ props.island.resources }}</div>
            <div class="ranking-summary-data-unit">㌧</div>
          </div>
        </div>
      </div>
      <div class="flex w-full border-t border-dashed max-md:flex-wrap">
        <div class="achievements-box">
          <AchievementIcons :achievement_props="props.island.achievements"></AchievementIcons>
        </div>
        <div class="island-comment-box" :class="{ 'text-on-surface-variant': hasComment }">
          <div class="comment-index">comment:</div>
          <div class="comment-text">{{ islandComment }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { AchievementProp } from '$js/entity//Achievement'
import AchievementIcons from '$vue/components/ui/AchievementIcons.vue'

interface IslandWithStatuses {
  id: number
  name: string
  owner_name: string
  development_points: number
  funds: number
  foods: number
  resources: number
  population: number
  funds_production_capacity: number
  foods_production_capacity: number
  resources_production_capacity: number
  environment: string
  area: number
  abandoned_turn: number
  comment: string
  achievements: AchievementProp[]
}

interface Props {
  index: number
  // TODO: ランキングのIslandデータ型は個別で定義しておいた方がよい？
  island: IslandWithStatuses
}

const props = defineProps<Props>()

const observer = ref<IntersectionObserver | null>(null)
const isAppeared = ref(false) // スクリーン上に出てきたかどうか

onMounted(() => {
  const target = document.querySelector('#ranking-' + props.island.id)

  observer.value = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) onAppeared()
    })
  })
  observer.value.observe(target)
})

const islandNameSize = computed(() => {
  const nameSize = props.island.name.length
  if (nameSize > 24) {
    return 'text-xs'
  } else if (nameSize > 16) {
    return 'text-sm'
  } else {
    return 'text-lg'
  }
})

const hasComment = computed(() => {
  return props.island.comment === null || props.island.comment === undefined || props.island.comment === ''
})

const islandComment = computed(() => {
  if (hasComment.value) {
    return 'コメントはありません'
  } else {
    return props.island.comment
  }
})

const onAppeared = () => {
  isAppeared.value = true
  observer.value.disconnect()

  // const target = document.getElementById("ranking-" + this.props.island.id);
  // target.animate({
  //     transform: ["translateX(-120%) translateZ(0)", "translateX(0) translateZ(0)"]
  // }, {
  //     duration: 800,
  //     easing: 'ease-in-out',
  //     fill: "both",
  // })
}
</script>

<style lang="scss" scoped>
.ranking {
  @apply mb-3 flex flex-wrap rounded-xl border bg-surface p-0 text-on-surface drop-shadow-md;

  &.active {
    @apply animate-slide-from-left;
  }

  .ranking-index {
    @apply flex items-center;
    @apply min-w-full max-w-full max-md:w-full max-md:py-2;
    @apply max-md:border-b-2 md:min-w-[25%] md:max-w-[25%] md:border-r-2;

    .ranking-index-num {
      @apply w-fit px-3 text-2xl font-black;
    }

    .ranking-index-info {
      @apply min-w-0 grow text-center;

      .ranking-index-name {
        @apply text-center font-black text-on-link;
      }

      .ranking-index-owner {
        @apply text-xs leading-none text-on-surface-variant;
      }
    }
  }

  .ranking-data {
    @apply min-w-0 grow pt-1 md:max-w-[75%];

    .ranking-summary {
      @apply grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5;

      .ranking-summary-title {
        @apply text-xs font-bold text-on-surface-variant underline;
      }

      .ranking-summary-wrapper {
        @apply pl-1 pr-3 md:border-r;
      }

      .ranking-summary-wrapper:nth-of-type(3) {
        @apply md:max-lg:border-none;
      }

      .ranking-summary-wrapper:nth-of-type(5) {
        @apply lg:border-none;
      }

      .ranking-summary-data {
        @apply flex items-end;

        .ranking-summary-data-num {
          @apply mr-2 inline-block grow text-right text-base font-bold;
          @apply md:text-sm;
          @apply lg:pb-1 lg:text-lg;
        }

        .ranking-summary-data-unit {
          @apply w-1/6 text-right text-xs;
          @apply lg:text-[0.6rem];
        }
      }
    }

    .achievements-box {
      @apply my-auto h-fit py-1 leading-none;
      @apply w-1/2 max-md:mx-auto;
      @apply md:w-1/5 md:border-r;
    }

    .island-comment-box {
      // general
      @apply w-full max-w-full px-2;
      // sp
      @apply max-md:my-2;
      // desktop
      @apply md:mt-1 md:w-4/5 md:py-1;

      .comment-index {
        @apply text-xs leading-none text-on-surface-variant;
      }

      .comment-text {
        @apply w-full text-sm leading-none text-on-surface;
      }
    }
  }
}
</style>
