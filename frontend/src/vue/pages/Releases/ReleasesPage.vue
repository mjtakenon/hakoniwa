<template>
  <div id="releases-page">
    <h1 class="title">更新履歴</h1>
    <div v-if="releases !== null" class="releases">
      <div v-for="release of releases" class="release">
        <div class="release-head">
          <h2 class="subtitle">{{ release.title }}</h2>
          <div class="update-at">更新日: {{ release.update_at }}</div>
        </div>
        <AsyncReleasesMarkdown :sources="release.body" />
      </div>
    </div>
    <div v-else>
      <div>更新情報はありません</div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineAsyncComponent, onMounted, ref } from 'vue'
import axios from 'axios'
import { formatDate } from '$js/Utils.js'

interface Release {
  title: string
  body: string
  update_at: string
}

const AsyncReleasesMarkdown = defineAsyncComponent(() => import('./ReleasesMarkdown.vue'))

const releases = ref<Release[] | null>([])
const MAX_LATEST_RELEASES = 3

const getReleases = async () => {
  await axios
    .get('https://api.github.com/repos/mjtakenon/hakoniwa/releases')
    .then((res) => {
      let count = 0
      for (const release of res.data) {
        if (release.draft) continue
        releases.value.push({
          title: release.name,
          body: release.body,
          update_at: formatDate(new Date(release.created_at))
        })
        count++
        if (count > MAX_LATEST_RELEASES) break
      }
    })
    .catch((err) => {
      console.debug(err)
      releases.value = null
    })
}

onMounted(() => {
  getReleases()
})
</script>

<style scoped lang="scss">
#releases-page {
  @apply mx-auto mt-10 min-h-[100vh] max-w-[800px];

  .title {
    @apply mb-6 border-b pb-2 max-md:px-4;
  }

  .releases {
    @apply px-3;

    .release {
      @apply mb-12;

      .release-head {
        @apply flex items-end justify-between border-b border-dashed;

        .subtitle {
          @apply text-xl font-bold;
        }
      }
    }
  }
}

.update-at {
  @apply my-2 text-right text-xs text-on-surface-variant md:text-sm;
}
</style>
