<template v-if="logs.length > 0">
  <div id="logs">
    <div class="subtitle">
      {{ title }}
    </div>
    <div class="turn-log" v-for="(log, logIndex) of logs" :key="'log-' + logIndex">
      <div class="turn-title">
        <div class="turn-title-inner">
          <span class="turn-title-inner-text">ターン</span>
          <span class="turn-title-inner-number">
            {{ log.turn }}
          </span>
        </div>
      </div>
      <div class="data-box">
        <div v-for="(line, lineIndex) of log.texts" :key="'line-' + logIndex + '-' + lineIndex" class="log">
          <div class="mr-1">・</div>
          <div class="message">
            <template v-for="(context, conIndex) of line" :key="'text-' + logIndex + '-' + lineIndex + '-' + conIndex">
              <a v-if="context.hasOwnProperty('link')" :href="context.link" :style="context.style">
                {{ context.text }}
              </a>
              <span v-else-if="context.text !== ''" :style="context.style">
                {{ context.text }}
              </span>
            </template>
          </div>
        </div>
        <div v-if="log.hasOwnProperty('summary')" class="turn-summaries">
          <div class="summary-box" :class="getBgColor(log.summary.foods)">
            <span class="summary-box-title">🍎食料</span>
            <span class="summary-box-num" :class="getTextColor(log.summary.foods)">
              {{
                log.summary.foods > 0 ? '+' + log.summary.foods.toLocaleString() : log.summary.foods.toLocaleString()
              }}
            </span>
            <span class="summary-box-unit">㌧</span>
          </div>

          <div class="summary-box" :class="getBgColor(log.summary.funds)">
            <span class="summary-box-title">💰資金</span>
            <span class="summary-box-num" :class="getTextColor(log.summary.funds)">
              {{
                log.summary.funds > 0 ? '+' + log.summary.funds.toLocaleString() : log.summary.funds.toLocaleString()
              }}
            </span>
            <span class="summary-box-unit">億円</span>
          </div>

          <div class="summary-box" :class="getBgColor(log.summary.resources)">
            <span class="summary-box-title">🏭資源</span>
            <span class="summary-box-num" :class="getTextColor(log.summary.resources)">
              {{
                log.summary.resources > 0
                  ? '+' + log.summary.resources.toLocaleString()
                  : log.summary.resources.toLocaleString()
              }}
            </span>
            <span class="summary-box-unit">㌧</span>
          </div>

          <div class="summary-box" :class="getBgColor(log.summary.population)">
            <span class="summary-box-title">👤人口</span>
            <span class="summary-box-num" :class="getTextColor(log.summary.population)">
              {{
                log.summary.population > 0
                  ? '+' + log.summary.population.toLocaleString()
                  : log.summary.population.toLocaleString()
              }}
            </span>
            <span class="summary-box-unit">人</span>
          </div>

          <div class="summary-box" :class="getBgColor(log.summary.points)">
            <span class="summary-box-title">⚡ポイント</span>
            <span class="summary-box-num" :class="getTextColor(log.summary.points)">
              {{
                log.summary.points > 0 ? '+' + log.summary.points.toLocaleString() : log.summary.points.toLocaleString()
              }}
            </span>
            <span class="summary-box-unit">pts</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onMounted, PropType, ref } from 'vue'
import { Log, LogParser, LogProps } from '../../../store/Entity/Log'

let logs = ref<Log[]>([])

interface Props {
  title?: String
  parsedLogs?: PropType<Log[]>
  unparsedLogs?: PropType<LogProps[]>
}

const props = defineProps<Props>()

const getBgColor = (num: number): string => {
  if (num > 0) return 'border-plus'
  if (num === 0) return 'border-surface-variant'
  return 'border-minus'
}

const getTextColor = (num: number): string => {
  if (num > 0) return 'text-on-plus'
  if (num === 0) return 'text-on-surface-variant'
  return 'text-on-minus'
}

onMounted(() => {
  if (props.unparsedLogs !== undefined) {
    const parser = new LogParser()
    logs.value = parser.parse(props.unparsedLogs)
  } else {
    logs.value = props.parsedLogs
  }
})
</script>

<style lang="scss" scoped>
#logs {
  // general
  @apply mt-10 w-full bg-surface text-left drop-shadow-md;
  @apply md:rounded-md;

  .subtitle {
    @apply mb-3 mt-0 w-full bg-primary px-3 py-3 font-bold text-on-primary md:rounded-t-md;
  }

  .turn-log {
    // general
    @apply flex items-start pb-3;
    // sp
    @apply mb-1 text-sm max-md:mt-4 max-md:flex-wrap max-md:pb-6;
    // desktop
    @apply mt-4 md:mx-5 md:border-b-2 md:border-b-surface-variant md:text-base lg:mb-0.5;

    .turn-title {
      // general
      @apply my-1;
      // sp
      @apply max-md:w-2/5 max-md:rounded-r-3xl max-md:bg-surface-variant max-md:px-2 max-md:py-1 max-md:drop-shadow;
      // desktop
      @apply md:border-r-2 md:border-surface-variant md:pr-2;

      .turn-title-inner {
        // general
        @apply text-center;
        // sp
        @apply w-full max-md:flex;
        // desktop
        @apply md:mr-2;

        .turn-title-inner-text {
          // general
          @apply text-xs text-on-surface-variant;
          // desktop
          @apply md:block;
        }

        .turn-title-inner-number {
          // general
          @apply mt-auto text-lg font-bold;
          // sp
          @apply max-md:grow max-md:text-center;
          // desktop
          @apply md:-mt-1 md:block;
        }
      }
    }

    .data-box {
      // sp
      @apply max-md:mt-2 max-md:w-full;
      // desktop
      @apply md:grow;

      .log {
        @apply flex pl-2;

        .message {
          @apply min-w-0 grow;
        }
      }

      .log:last-child {
        @apply mb-3;
      }

      .turn-summaries {
        // general
        @apply mt-2 grid gap-2 text-left;
        // sp
        @apply grid-cols-2 max-md:mt-3;
        // desktop
        @apply md:grid-cols-5 md:pl-[10%] lg:pl-[25%];

        .summary-box {
          @apply flex w-full items-center border-b-2 px-0.5;

          .summary-box-title {
            @apply mr-1 text-[0.6rem] text-on-surface-variant;
          }

          .summary-box-num {
            @apply grow text-right text-sm font-bold;
          }

          .summary-box-unit {
            @apply ml-1 text-right text-[0.6rem] text-on-surface-variant;
          }
        }
      }
    }
  }

  .turn-log:last-child {
    @apply border-none;
  }
}
</style>
