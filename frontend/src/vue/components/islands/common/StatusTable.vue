<template>
  <div class="stats">
    <div class="stats-header">
      <div class="names">
        <h1 class="island-name">{{ props.island.name }}島</h1>
        <span class="owner-name">({{ props.island.owner_name }})</span>
      </div>
      <div class="header-achievements">
        <AchievementIcons :achievement_data="props.achievements"></AchievementIcons>
      </div>
    </div>
    <div class="stats-contents">
      <div class="stats-island">
        <div class="data-point stat-box-island border-b">
          <div class="stat-box-title">発展ポイント</div>
          <div class="stat-inner">
            <div class="stat-box-num">
              {{ props.status.development_points.toLocaleString() }}
            </div>
            <div class="stat-box-unit">pts</div>
          </div>
        </div>
        <div class="data-area stat-box-island border-b">
          <div class="stat-box-title">面積</div>
          <div class="stat-inner">
            <div class="stat-box-num">
              {{ props.status.area.toLocaleString() }}
            </div>
            <div class="stat-box-unit">万坪</div>
          </div>
        </div>
        <div class="data-environment stat-box-island border-b">
          <div class="stat-box-title">環境</div>
          <div class="stat-inner environment">
            <div class="stat-box-num">
              {{ getEnvironmentString(props.status.environment) }}
            </div>
          </div>
        </div>
      </div>
      <div class="stats-info">
        <div class="stats-summary">
          <div class="stats-subtitle">島の情報</div>
          <div class="stats-summary-inner">
            <div class="stat-box-info">
              <FontAwesomeIcon icon="fa-solid fa-sack-dollar" class="stat-box-icon" />
              <div class="stat-box-title">資金</div>
              <div class="stat-box-num">{{ props.status.funds.toLocaleString() }}</div>
              <div class="stat-box-unit">億円</div>
            </div>
            <div class="stat-box-info">
              <FontAwesomeIcon icon="fa-solid fa-wheat-awn" class="stat-box-icon" />
              <div class="stat-box-title">食料</div>
              <div class="stat-box-num">{{ props.status.foods.toLocaleString() }}</div>
              <div class="stat-box-unit">㌧</div>
            </div>
            <div class="stat-box-info">
              <FontAwesomeIcon icon="fa-solid fa-oil-well" class="stat-box-icon" />
              <div class="stat-box-title">資源</div>
              <div class="stat-box-num">{{ props.status.resources.toLocaleString() }}</div>
              <div class="stat-box-unit">㌧</div>
            </div>
          </div>
        </div>
        <div class="stats-human">
          <div class="stats-subtitle">人的資源</div>
          <div class="stats-human-inner">
            <div class="stats-human-left">
              <div class="stat-box-human-left population">
                <div class="stat-box-title">総人口</div>
                <div class="stat-inner">
                  <div class="stat-box-num">
                    {{ props.status.population.toLocaleString() }}
                  </div>
                  <div class="stat-box-unit">人</div>
                </div>
              </div>
              <div class="stat-box-human-left unassigned" :class="{ plus: calcUnassigned > 0 }">
                <div class="stat-box-title">未割当</div>
                <div class="stat-inner">
                  <div class="stat-box-num">
                    {{ calcUnassigned.toLocaleString() }}
                  </div>
                  <div class="stat-box-unit">人</div>
                </div>
              </div>
            </div>
            <div class="stats-human-right">
              <div class="stat-box-info human">
                <FontAwesomeIcon icon="fa-solid fa-wheat-awn" class="stat-box-icon" />
                <div class="stat-box-title">農業</div>
                <div class="stat-box-num">
                  {{ props.status.foods_production_capacity.toLocaleString() }}
                </div>
                <div class="stat-box-unit">人</div>
              </div>
              <div class="stat-box-info human">
                <FontAwesomeIcon icon="fa-solid fa-sack-dollar" class="stat-box-icon" />
                <div class="stat-box-title">工業</div>
                <div class="stat-box-num">
                  {{ props.status.funds_production_capacity.toLocaleString() }}
                </div>
                <div class="stat-box-unit">人</div>
              </div>
              <div class="stat-box-info human">
                <FontAwesomeIcon icon="fa-solid fa-oil-well" class="stat-box-icon" />
                <div class="stat-box-title">資源生産</div>
                <div class="stat-box-num">
                  {{ props.status.resources_production_capacity.toLocaleString() }}
                </div>
                <div class="stat-box-unit">人</div>
              </div>
              <div class="stat-box-info human">
                <FontAwesomeIcon icon="fa-solid fa-shield" class="stat-box-icon" />
                <div class="stat-box-title">軍事</div>
                <div class="stat-box-num">
                  {{ props.status.maintenance_number_of_people.toLocaleString() }}
                </div>
                <div class="stat-box-unit">人</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="stat-comment">
      <div class="stat-comment-title">コメント</div>
      <div
        v-if="props.island.comment === '' || props.island.comment === undefined || props.island.comment === null"
        class="stat-comment-empty">
        コメントはありません
      </div>
      <div v-else class="stat-comment-main">
        {{ props.island.comment }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import AchievementIcons from '../../ui/AchievementIcons.vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faOilWell, faSackDollar, faShield, faWheatAwn } from '@fortawesome/free-solid-svg-icons'
import { getEnvironmentString } from '$store/Entity/Island.js'
import { Status } from '$store/Entity/Status.js'
import { AchievementProp } from '$store/Entity/Achievement.js'

library.add(faSackDollar, faWheatAwn, faOilWell, faShield)

interface Props {
  island: {
    id: number
    name: string
    owner_name: string
    comment?: string
  }
  status: Status
  achievements: AchievementProp[]
}

const props = defineProps<Props>()

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

const calcUnassigned = computed(() => {
  return (
    props.status.population -
    props.status.foods_production_capacity -
    props.status.funds_production_capacity -
    props.status.resources_production_capacity -
    props.status.maintenance_number_of_people
  )
})
</script>

<style lang="scss" scoped>
.stats {
  @apply container mx-auto mb-4 w-full rounded-lg bg-surface text-on-surface drop-shadow-md;
  @apply md:px-4 md:py-2;

  .stats-header {
    @apply mb-4 w-full border-b-2 border-dashed py-3;
    @apply md:flex;

    .names {
      @apply flex min-w-0 grow items-end justify-center;
      @apply max-md:w-full;

      .island-name {
        @apply mr-2 text-2xl font-bold;
      }

      .owner-name {
        @apply text-sm text-on-surface-variant;
      }
    }

    .header-achievements {
      @apply w-full;
      @apply md:w-fit md:min-w-[20%] md:border-l;
    }
  }

  .stats-contents {
    @apply w-full;
    @apply max-md:px-2;

    .stat-box-island {
      @apply py-1;

      .stat-box-title {
        @apply text-left text-sm font-bold leading-none text-on-surface-variant;
      }

      .stat-inner {
        @apply mt-auto flex flex-wrap items-end text-right;

        &.environment {
          @apply mt-2;
        }

        .stat-box-num {
          @apply min-w-0 grow text-right font-bold text-on-surface;
          @apply text-sm sm:text-lg;
          @apply leading-none max-md:mt-auto max-md:w-full max-md:px-2;
          @apply md:text-xl md:leading-none;
        }

        .stat-box-unit {
          @apply ml-2 leading-none text-on-surface-variant;
          @apply max-md:w-full max-md:text-xs max-md:leading-none;
        }
      }
    }

    .stat-box-info {
      @apply mb-1.5 flex items-center rounded-lg bg-surface-variant px-2 py-1 text-on-surface-variant;
      @apply max-md:w-1/3 max-md:flex-wrap;
      @apply md:gap-2;

      &.human {
        @apply max-md:w-full;
      }

      .stat-box-icon {
        @apply text-sm;
        @apply md:text-xl;
      }

      .stat-box-title {
        @apply font-bold;
        @apply text-xs;
        @apply md:text-sm;
      }

      .stat-box-num {
        @apply min-w-0 grow text-right font-bold;
        @apply max-md:w-full max-md:px-1;
        @apply text-sm sm:text-lg sm:leading-none md:text-lg md:leading-none;
      }

      .stat-box-unit {
        @apply mt-auto text-right;
        @apply text-[0.5rem] leading-none max-md:w-full;
        @apply md:w-[24px] md:text-xs;
      }
    }

    .stat-box-human-left {
      @apply mb-2 flex w-full grow flex-wrap rounded-lg px-2 py-1;

      .stat-box-title {
        @apply w-full text-left text-sm font-bold leading-none text-on-surface-variant;
      }

      .stat-inner {
        @apply flex w-full flex-wrap items-end gap-0 pb-2;

        .stat-box-num {
          @apply min-w-0 grow text-right text-xl font-bold leading-none text-on-surface;
          @apply text-lg leading-none max-md:mt-auto max-md:w-full max-md:px-2;
          @apply text-lg md:text-xl;
        }

        .stat-box-unit {
          @apply ml-2 text-right leading-none text-on-surface-variant;
          @apply max-md:w-full max-md:text-xs max-md:leading-none;
        }
      }

      &.population {
        @apply bg-primary;

        .stat-box-title,
        .stat-box-unit {
          @apply text-on-primary;
        }

        .stat-box-num {
          @apply text-surface;
        }
      }

      &.unassigned {
        @apply bg-primary-container;

        .stat-box-title,
        .stat-box-unit {
          @apply text-on-primary-container;
        }

        .stat-box-num {
          @apply text-on-surface;
        }

        &.plus {
          @apply bg-minus text-on-minus dark:bg-on-minus dark:text-minus;

          .stat-box-title,
          .stat-box-unit,
          .stat-box-num {
            @apply text-on-minus dark:text-minus;
          }
        }
      }
    }

    .stats-island {
      @apply flex;
      @apply mb-2 gap-3;
      @apply md:mb-6 md:gap-8;

      .data-point {
        @apply w-2/5;
        @apply md:w-1/2;
      }

      .data-area {
        @apply w-2/5;
        @apply md:w-1/4;
      }

      .data-environment {
        @apply w-1/5;
        @apply md:w-1/4;
      }
    }

    .stats-info {
      @apply flex;
      @apply max-md:flex-wrap;
      @apply md:gap-10;

      .stats-subtitle {
        @apply text-left text-sm font-bold text-on-surface-variant;
        @apply max-md:w-full;
      }

      .stats-summary {
        @apply w-full;
        @apply md:w-1/3;

        .stats-summary-inner {
          @apply max-md:flex max-md:gap-1;
        }
      }

      .stats-human {
        @apply w-full;
        @apply md:w-2/3;

        .stats-human-inner {
          @apply flex;
          @apply gap-2;
          @apply md:gap-4;

          .stats-human-left {
            @apply md:flex md:flex-wrap;
          }

          .stats-human-left,
          .stats-human-right {
            @apply w-1/2;
          }
        }
      }
    }
  }

  .stat-comment {
    @apply mt-2 border-t px-2 pb-2;

    .stat-comment-title {
      @apply text-left text-sm font-black;
    }

    .stat-comment-empty {
      @apply text-sm text-on-secondary-container;
    }

    .stat-comment-main {
      @apply px-2 text-left;
    }
  }
}
</style>
