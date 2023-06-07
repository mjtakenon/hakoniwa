<template>
    <div id="bbs">
        <div class="header">
            <font-awesome-icon class="mr-6" :icon="['fas', 'chalkboard-user']" size="xl"/>
            <span>{{ store.island.name }}島の掲示板</span>
        </div>
        <div class="bbs-form">
            <div class="bbs-form-inner">
                <div class="bbs-form-title">掲示板送信</div>
                <div class="bbs-input-box">
                    <input id="bbs-input" type="text" name="bbs-input" maxlength="128" minlength="0" v-model="comment" @blur="checkInput">
                    <div v-if="isSubmitting" class="bbs-input-notify updating">
                        <div class="update-circle"><div class="update-circle-spin"></div></div>
                        <span>送信中...</span>
                    </div>
                    <div v-else-if="formError !== ''" class="bbs-input-notify error">{{formError}}</div>
                </div>
                <button
                    class="button-public"
                    :class="{'active': sendMode === 'public'}"
                    @click="changeSendMode('public')"
                >
                    通常
                </button>
                <button
                    class="button-private"
                    :class="{'active': sendMode === 'private'}"
                    @click="changeSendMode('private')"
                >
                    秘密通信(1000億円)
                </button>
                <button
                    class="bbs-submit"
                    :class="[sendMode === 'public' ? 'public' : 'private']"
                    @click="bbsSubmit"
                    :disabled="isSubmitting || hasError"
                >
                    送信
                </button>
            </div>
        </div>
        <div class="viewer">
            <div class="viewer-title">投稿一覧</div>
            <div v-show="this.store.bbs.length === 0" class="no-post">投稿はありません</div>
            <template v-for="post of this.store.bbs">
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
                            <div class="post-island-name">({{ post.island.name }}島)</div>
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
import {BbsMessage, BbsVisibility} from "../store/Entity/Bbs";
import {ErrorType, RequestStatus} from "../store/Entity/Network";

export default defineComponent({
    data() {
        return {
            sendMode: "public" as BbsVisibility,
            comment: "",
            formError: "",
            submitStatus: RequestStatus.None as RequestStatus,
        }
    },
    setup() {
        library.add(faChalkboardUser, faTrashCan)
        const store = useMainStore();
        return {store};
    },
    computed: {
        isSubmitting() {
            return this.submitStatus === RequestStatus.Updating;
        },
        hasError() {
            return this.formError !== "";
        }
    },
    methods: {
        changeSendMode(mode: BbsVisibility) {
            this.sendMode = mode;
        },
        async bbsSubmit() {
            this.checkInput();
            if(this.hasError || this.isSubmitting) return;
            this.submitStatus = RequestStatus.Updating;
            const result = await this.store.postBbs(this.comment, this.sendMode);
            this.submitStatus = result.status;

            if(result.status === RequestStatus.Success) {
                this.comment = "";
            }

            if(result.status === RequestStatus.Failed) {
                if (result.error === ErrorType.LackOfFunds) {
                    this.formError = "資金が不足しています";
                } else if (result.error === ErrorType.NotFound) {
                    this.formError = "島が見つかりません";
                } else if (result.error === ErrorType.TooManyRequests) {
                    this.formError = "時間をおいてから送信してください"
                } else {
                    this.formError = "不明なエラーです";
                }
            }
        },
        checkInput() {
            this.comment = this.comment.trim();
            this.formError = "";
            if(this.comment === "") {
                this.formError = "入力されていません";
            }
        }
    }
})
</script>

<style scoped lang="scss">
#bbs {
    @apply w-full bg-surface drop-shadow-md my-10 rounded-md py-5;

    .header {
        @apply flex items-center justify-start px-4 font-bold text-left text-xl text-on-surface-variant;
    }

    .bbs-form {
        @apply w-full my-4;
        @apply px-1;
        @apply md:px-8　md:mb-10;

        .bbs-form-inner {
            @apply flex flex-wrap bg-surface-variant text-on-surface-variant py-2 rounded-md;
            @apply px-2;
            @apply md:px-4;

            .bbs-form-title {
                @apply w-full text-left text-sm font-bold mb-1;
            }

            .bbs-input-box {
                @apply relative;
                @apply max-md:w-full max-md:mb-2;
                @apply md:grow md:min-w-0;

                #bbs-input {
                    @apply w-full rounded-lg px-2;
                }

                .bbs-input-notify {
                    @apply absolute w-fit top-0 right-0 text-right my-0.5 py-0.5 px-2 mr-2 text-xs rounded-md;

                    &.error{
                        @apply bg-error-container text-on-error-container;
                    }

                    &.updating {
                        @apply bg-primary-container text-primary;

                        .update-circle {
                            @apply inline-flex justify-center;

                            .update-circle-spin {
                                @apply block animate-spin m-1 w-2 h-2 border border-primary rounded-full border-t-transparent;
                            }
                        }
                    }
                }
            }

            .button-public, .button-private {
                @apply py-0 px-2 text-sm border-none font-bold bg-surface text-on-surface drop-shadow-none rounded-full;
                @apply max-md:mr-2;
                @apply md:ml-2
            }

            .button-public.active {
                @apply bg-primary text-on-primary;
            }

            .button-private.active {
                @apply bg-secondary text-on-secondary;
            }

            .bbs-submit {
                @apply py-0 px-2 text-sm font-bold drop-shadow-none;
                @apply max-md:ml-auto;
                @apply md:ml-10;

                &.public {
                    @apply bg-primary-container hover:bg-primary text-on-primary-container hover:text-on-primary border-on-primary-container;
                }

                &.private {
                    @apply bg-secondary-container hover:bg-secondary text-on-secondary-container hover:text-on-secondary border-on-secondary-container;
                }
            }
        }
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
                    @apply max-md:mr-auto;
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
                    @apply inline-flex items-center rounded-full px-2 text-xs font-bold leading-none mr-2;

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
