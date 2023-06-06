<template>
    <div id="bbs">
        <div class="header">
            <font-awesome-icon class="mr-6" :icon="['fas', 'chalkboard-user']" size="xl"/>
            <span>{{ store.island.name }}島の掲示板</span>
        </div>
        <div class="viewer">
            <div class="post">
                <div class="post-header">
                    <div class="post-turn">
                        <span class="turn-title">turn:</span>
                        <span class="turn-num">12345</span>
                    </div>
                    <a class="post-profile" href="/islands/1"> <!-- TODO: IDにする -->
                        <div class="post-island-owner">あいうえお</div>
                        <div class="post-island-name">
                            (かきくけこ島)
                        </div>
                    </a>
                    <div class="post-island-badge">
                        <div class="badge-text">島のオーナー</div>
                    </div>
                    <div class="delete-button">
                        <font-awesome-icon class="icon" :icon="['fas', 'trash-can']" />
                    </div>
                </div>
                <div class="post-contents">
                    <div class="post-comment">
                        こんにちは！これは島のBBSメッセージです。
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue'
import {useMainStore} from "../store/MainStore";
import {library} from "@fortawesome/fontawesome-svg-core";
import {faChalkboardUser, faTrashCan} from "@fortawesome/free-solid-svg-icons";

export default defineComponent({
    setup() {
        library.add(faChalkboardUser, faTrashCan)
        const store = useMainStore();
        return {store};
    }
})
</script>

<style scoped lang="scss">
#bbs {
    @apply w-full bg-surface border drop-shadow-md my-10 rounded;

    .header {
        @apply flex items-center justify-start px-4 font-bold text-left text-xl my-4 text-on-surface-variant mb-10;
    }

    .viewer {
        @apply my-4;
        @apply px-4;
        @apply md:px-8;

        .post {
            @apply border rounded-lg overflow-hidden;

            .post-header {
                @apply flex flex-wrap px-4 py-1 w-full bg-primary-container;

                .post-turn {
                    @apply max-md:text-left;
                    @apply md:border-r md:border-primary md:pr-3 md:mr-3 md:my-auto;

                    .turn-title {
                        @apply text-xs;
                    }
                    .turn-num {
                        @apply font-bold
                    }
                }

                .post-profile {
                    @apply max-md:w-full max-md:text-center max-md:min-w-0 max-md:order-2 max-md:my-1 mr-2;
                    @apply md:inline-flex md:mr-5;

                    .post-island-owner {
                        @apply inline font-bold mr-1 text-on-surface;
                    }

                    .post-island-name {
                        @apply inline mt-auto text-on-link text-sm font-bold;
                    }
                }

                .post-island-badge {
                    @apply inline-flex items-center rounded-full bg-primary px-2;
                    @apply max-md:ml-auto max-md:mr-2;

                    .badge-text {
                        @apply block m-auto text-xs text-on-primary font-bold leading-none;
                    }
                }

                .delete-button {
                    @apply bg-error px-1 rounded-md;
                    @apply md:ml-auto;

                    .icon {
                        @apply text-sm text-on-error;
                    }
                }
            }

            .post-contents {
                @apply px-8 py-2 text-left;

                .post-comment {
                    @apply text-sm;
                }
            }
        }
    }
}

</style>
