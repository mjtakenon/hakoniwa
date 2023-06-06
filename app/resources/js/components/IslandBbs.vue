<template>
    <div id="bbs">
        <div class="header">
            <font-awesome-icon class="mr-6" :icon="['fas', 'chalkboard-user']" size="xl"/>
            <span>{{ store.island.name }}島の掲示板</span>
        </div>
        <div class="viewer">
            <div class="viewer-title">投稿一覧</div>
            <div v-show="posts.length === 0" class="no-post">投稿はありません</div>
            <template v-for="post of posts">
                <div v-if="post.comment !== null && post.comment !== undefined" class="post"
                     :class="[post.visibility === 'public' ? 'border-primary-container' : 'border-secondary-container']">
                    <div class="post-header"
                         :class="[post.visibility === 'public' ? 'bg-primary-container' : 'bg-secondary-container']">
                        <div class="post-turn">
                            <span class="turn-title">turn:</span>
                            <span class="turn-num">{{ post.turn }}</span>
                        </div>
                        <a class="post-profile" :href="'/islands/' + post.island.id">
                            <div class="post-island-owner">{{ post.island.owner_name }}</div>
                            <div class="post-island-name">({{ post.island.name }})</div>
                        </a>
                        <div v-show="post.island.id === store.island.id" class="post-badge owner">
                            <div class="badge-text">島のオーナー</div>
                        </div>
                        <div v-show="post.visibility === 'private'" class="post-badge private">
                            <div class="badge-text">秘密通信</div>
                        </div>
                        <div v-show="post.user_id === store.user.user_id" class="delete-button">
                            <font-awesome-icon class="icon" :icon="['fas', 'trash-can']"/>
                        </div>
                    </div>
                    <div class="post-contents">
                        <div class="post-comment">
                            {{ post.comment }}
                        </div>
                    </div>
                </div>
                <div v-else-if="post.deleted" class="post-deleted">
                    [このメッセージは削除されました]
                </div>
                <div v-else class="post-private-hidden">
                    [秘密通信]
                </div>
            </template>

        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue'
import {useMainStore} from "../store/MainStore";
import {library} from "@fortawesome/fontawesome-svg-core";
import {faChalkboardUser, faTrashCan} from "@fortawesome/free-solid-svg-icons";
import {BbsMessage} from "../store/Entity/Bbs";

export default defineComponent({
    data() {
        return {
            posts: [
                {
                    id: 1,
                    user_id: 1,
                    turn: 1,
                    island: {
                        id: 1,
                        name: "あいうえお島",
                        owner_name: "かきくけこ",
                    },
                    comment: "これはメッセージです",
                    visibility: "public",
                    deleted: false
                },
                {
                    id: 2,
                    user_id: 3,
                    turn: 2,
                    island: {
                        id: 2,
                        name: "あ島",
                        owner_name: "まみむめも",
                    },
                    comment: "WWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWW",
                    visibility: "private",
                    deleted: false
                },
                {
                    id: 3,
                    user_id: 1,
                    visibility: "private",
                    deleted: true
                },
                {
                    id: 3,
                    user_id: 1,
                    visibility: "private",
                    deleted: false
                },
            ] as BbsMessage[]
        }
    },
    setup() {
        library.add(faChalkboardUser, faTrashCan)
        const store = useMainStore();
        return {store};
    },
    methods: {}
})
</script>

<style scoped lang="scss">
#bbs {
    @apply w-full bg-surface drop-shadow-md my-10 rounded-md py-5;

    .header {
        @apply flex items-center justify-start px-4 font-bold text-left text-xl text-on-surface-variant;
    }

    .viewer {
        @apply my-4;
        @apply px-4;
        @apply md:px-8;

        .viewer-title {
            @apply w-fit px-2 py-1 text-sm rounded-lg text-left mb-2 font-bold bg-surface-variant text-on-surface-variant;
        }

        .no-post {
            @apply my-5 text-on-surface-variant;
        }

        .post {
            @apply border rounded-lg overflow-hidden mb-6;

            .post-header {
                @apply flex flex-wrap px-4 py-1 w-full;

                .post-turn {
                    @apply text-left;
                    @apply md:w-1/12 md:pr-3 md:mr-3 md:my-auto;

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

                .post-badge {
                    @apply inline-flex items-center rounded-full px-2 text-xs font-bold leading-none;
                    @apply max-md:ml-auto max-md:mr-2;

                    &.owner {
                        @apply bg-primary text-on-primary;
                    }

                    &.private {
                        @apply bg-secondary text-on-secondary
                    }

                    .badge-text {
                        @apply block m-auto;
                    }
                }

                .delete-button {
                    @apply bg-error dark:bg-on-error px-1 rounded-md;
                    @apply md:ml-auto;

                    .icon {
                        @apply text-sm text-on-error dark:text-error;
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

        .post-deleted {
            @apply w-fit mx-auto px-3 py-1 text-center text-sm rounded-lg bg-error-container text-error mb-3;
        }

        .post-private-hidden {
            @apply w-fit mx-auto px-3 py-1 text-center text-sm rounded-lg bg-alert-container text-alert mb-3;
        }
    }
}

</style>
