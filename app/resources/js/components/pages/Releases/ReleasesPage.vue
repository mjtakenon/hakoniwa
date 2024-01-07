<template>
    <div id="releases-page">
        <h1 class="title">更新履歴</h1>
        <div v-if="releases !== null" class="releases">
            <div v-for="release of releases" class="release">
                <div class="release-head">
                    <h2 class="subtitle">{{release.title}}</h2>
                    <div class="update-at">更新日: {{release.update_at}}</div>
                </div>
                <ReleasesMarkdown :sources="release.body"></ReleasesMarkdown>
            </div>
        </div>
        <div v-else>
            <div>更新情報はありません</div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineAsyncComponent, defineComponent} from 'vue'
import axios from "axios";
import {formatDate} from "../../../Utils";

interface Release {
    title: string,
    body: string,
    update_at: string,
}

export default defineComponent({
    components: {
        ReleasesMarkdown: defineAsyncComponent(() =>
            import("./ReleasesMarkdown.vue")
        )
    },
    data() {
        return {
            releases: [] as Release[] | null,
            MAX_LATEST_RELEASES: 3,
        }
    },
    mounted() {
        this.getReleases();
    },
    methods: {
        async getReleases() {
            await axios.get("https://api.github.com/repos/mjtakenon/hakoniwa/releases")
                .then(res => {
                    let count = 0;
                    for(const release of res.data) {
                        if (release.draft) continue;
                        this.releases.push({
                            title: release.name,
                            body: release.body,
                            update_at: formatDate(new Date(release.created_at))
                        });
                        count++;
                        if(count > this.MAX_LATEST_RELEASES) break;
                    }
                })
                .catch(err => {
                    console.debug(err);
                    this.releases = null;
                })
        }
    }
})
</script>

<style scoped lang="scss">
#releases-page {
    @apply min-h-[100vh] max-w-[800px] mx-auto mt-10;

    .title {
        @apply border-b pb-2 mb-6 max-md:px-4;
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
    @apply my-2 text-right text-on-surface-variant text-xs md:text-sm;
}
</style>
