<template>
  <div :class="['infobox', props.type]">
    <FontAwesomeIcon class="info-icon" :icon="icon" />
    <div class="info-contents">
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import { faCircleExclamation, faTriangleExclamation, faCircleXmark } from '@fortawesome/free-solid-svg-icons'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { computed } from 'vue'

library.add(faCircleExclamation, faTriangleExclamation, faCircleXmark)

type InfoboxType = 'notify' | 'alert' | 'error'

type Props = {
  type: InfoboxType
  faIcon?: string[]
}
const props = defineProps<Props>()

const icon = computed<string[]>(() => {
  if (props.faIcon == null) {
    switch (props.type) {
      case 'notify':
        return ['fas', 'circle-exclamation']
      case 'alert':
        return ['fas', 'triangle-exclamation']
      case 'error':
        return ['fas', 'circle-xmark']
    }
  } else {
    return props.faIcon
  }
})
</script>

<style scoped lang="scss">
.infobox {
  @apply my-3 flex gap-3 rounded p-4 text-base font-bold drop-shadow-md items-center justify-center;
  @apply md:gap-6;

  .info-icon {
    @apply h-7 w-7;
  }

  .info-contents {
    @apply w-full;
  }

  &.notify {
    @apply bg-secondary-container;

    .info-icon {
      @apply text-secondary;
    }

    .info-contents {
      @apply  text-on-secondary-container;
    }
  }

  &.alert {
    @apply bg-alert-container;

    .info-icon {
      @apply text-alert;
    }

    .info-contents {
      @apply  text-on-alert-container;
    }
  }

  &.error {
    @apply bg-error-container;

    .info-icon {
      @apply text-error;
    }

    .info-contents {
      @apply  text-on-error-container;
    }
  }
}
</style>
