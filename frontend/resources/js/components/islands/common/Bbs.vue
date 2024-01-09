<template>
  <div id="bbs">
    <div class="header">
      <FontAwesomeIcon class="mr-6" :icon="['fas', 'chalkboard-user']" size="xl" />
      <span>{{ props.island.name }}島の掲示板</span>
    </div>
    <div v-if="userStore.hasIsland" class="bbs-form">
      <div class="bbs-form-inner">
        <div class="bbs-form-title">掲示板送信</div>
        <div class="bbs-input-box">
          <input
            id="bbs-input"
            type="text"
            name="bbs-input"
            maxlength="128"
            minlength="0"
            v-model="comment"
            @blur="checkInput()" />
          <div v-if="isSubmitting" class="bbs-input-notify updating">
            <div class="update-circle">
              <div class="update-circle-spin"></div>
            </div>
            <span>送信中...</span>
          </div>
          <div v-else-if="formError !== ''" class="bbs-input-notify error">{{ formError }}</div>
        </div>
        <button class="button-public" :class="{ active: sendMode === 'public' }" @click="changeSendMode('public')">
          全体
        </button>
        <button
          v-if="props.island.id !== userStore.user?.island?.id"
          class="button-private"
          :class="{ active: sendMode === 'private' }"
          @click="changeSendMode('private')">
          秘密(1000億円)
        </button>
        <button
          class="bbs-submit"
          :class="[sendMode === 'public' ? 'public' : 'private']"
          @click="bbsSubmit()"
          :disabled="isSubmitting || hasError">
          送信
        </button>
      </div>
    </div>
    <div class="viewer">
      <div class="viewer-title">投稿一覧</div>
      <div v-show="bbsStore.bbs.length === 0" class="no-post">投稿はありません</div>
      <template v-for="post of bbsStore.bbs">
        <div
          v-if="post.comment !== null && post.comment !== undefined"
          class="post"
          :class="[post.visibility === 'public' ? 'border-primary-container' : 'border-secondary-container']">
          <div
            class="post-header"
            :class="[post.visibility === 'public' ? 'bg-primary-container' : 'bg-secondary-container']">
            <div class="post-turn">
              <span class="turn-title">ターン: </span>
              <span class="turn-num">{{ post.turn }}</span>
            </div>
            <a v-if="post.user.island?.id !== undefined" class="post-profile" :href="'/islands/' + post.user.island.id">
              <div class="post-island-owner">{{ post.user.island.owner_name }}</div>
              <div class="post-island-name">({{ post.user.island.name }}島)</div>
            </a>
            <div v-else class="post-profile">
              <div class="post-island-owner">削除された島</div>
            </div>
            <div v-show="post.user.island?.id === props.island.id" class="post-badge owner">
              <div class="badge-text">島のオーナー</div>
            </div>
            <div v-show="post.visibility === 'private'" class="post-badge private">
              <div class="badge-text">秘密通信</div>
            </div>
            <div v-show="post.user.id === userStore.user?.id" class="delete-button" @click="deleteComment(post)">
              <FontAwesomeIcon class="icon pointer-events-none" :icon="['fas', 'trash-can']" />
            </div>
          </div>
          <div class="post-contents">
            <div class="post-delete-error">
              {{ post.errorMessage }}
            </div>
            <div class="post-comment">
              {{ post.comment }}
            </div>
          </div>
        </div>
        <div v-else-if="post.deleted" class="post-deleted">[このメッセージは削除されました]</div>
        <div v-else class="post-private-hidden">[秘密通信]</div>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { faChalkboardUser, faTrashCan } from '@fortawesome/free-solid-svg-icons'
import { BbsMessage, BbsVisibility } from '../../../store/Entity/Bbs'
import { ErrorType, RequestStatus } from '../../../store/Entity/Network'
import { useBbsStore } from '../../../store/BbsStore.js'
import { useUserStore } from '../../../store/UserStore.js'
import { Island } from '../../../store/Entity/Island.js'

const bbsStore = useBbsStore()
const userStore = useUserStore()

interface Props {
  island: Island
}

const props = defineProps<Props>()

const sendMode = ref<BbsVisibility>('public')
const comment = ref('')
const formError = ref('')
const submitStatus = ref<RequestStatus>(RequestStatus.None)
const deleteStatus = ref<RequestStatus>(RequestStatus.None)

library.add(faChalkboardUser, faTrashCan)

const isSubmitting = computed(() => {
  return submitStatus.value === RequestStatus.Updating
})

const hasError = computed(() => {
  return formError.value !== ''
})

const changeSendMode = (mode: BbsVisibility) => {
  sendMode.value = mode
}

const bbsSubmit = async () => {
  checkInput()
  if (hasError.value || isSubmitting.value) return
  submitStatus.value = RequestStatus.Updating
  const result = await bbsStore.postBbs(comment.value, sendMode.value, props.island)
  submitStatus.value = result.status

  if (result.status === RequestStatus.Success) {
    comment.value = ''
  }

  if (result.status === RequestStatus.Failed) {
    if (result.error === ErrorType.LackOfFunds) {
      formError.value = '資金が不足しています'
    } else if (result.error === ErrorType.NotFound) {
      formError.value = '島が見つかりません'
    } else if (result.error === ErrorType.TooManyRequests) {
      formError.value = '時間をおいてから送信してください'
    } else {
      formError.value = '不明なエラーです'
    }
  }
}

const deleteComment = async (target: BbsMessage) => {
  if (deleteStatus.value === RequestStatus.Updating) return
  deleteStatus.value = RequestStatus.Updating
  const result = await bbsStore.deleteBbs(target, props.island)
  deleteStatus.value = result.status

  if (deleteStatus.value === RequestStatus.Failed) {
    target.errorMessage = 'コメント削除時にエラーが発生しました'
  }
}

const checkInput = () => {
  comment.value = comment.value.trim()
  if (comment.value === '') {
    formError.value = '入力されていません'
    return
  }
  formError.value = ''
}
</script>

<style scoped lang="scss">
#bbs {
  @apply my-10 w-full rounded-md bg-surface py-5 drop-shadow-md;

  .header {
    @apply flex items-center justify-start px-4 text-left text-xl font-bold text-on-surface-variant;
  }

  .bbs-form {
    @apply my-4 w-full;
    @apply px-1;
    @apply md:px-8　md:mb-10;

    .bbs-form-inner {
      @apply flex flex-wrap rounded-md bg-surface-variant py-2 text-on-surface-variant;
      @apply px-2;
      @apply md:px-4;

      .bbs-form-title {
        @apply mb-1 w-full text-left text-sm font-bold;
      }

      .bbs-input-box {
        @apply relative;
        @apply max-md:mb-2 max-md:w-full;
        @apply md:min-w-0 md:grow;

        #bbs-input {
          @apply w-full rounded-lg bg-background px-2;
        }

        .bbs-input-notify {
          @apply absolute right-0 top-0 my-0.5 mr-2 w-fit rounded-md px-2 py-0.5 text-right text-xs;

          &.error {
            @apply bg-error-container text-on-error-container;
          }

          &.updating {
            @apply bg-primary-container text-primary;

            .update-circle {
              @apply inline-flex justify-center;

              .update-circle-spin {
                @apply m-1 block h-2 w-2 animate-spin rounded-full border border-primary border-t-transparent;
              }
            }
          }
        }
      }

      .button-public,
      .button-private {
        @apply rounded-full border-none bg-surface px-2 py-0 text-sm font-bold text-on-surface drop-shadow-none;
        @apply max-md:mr-2;
        @apply md:ml-2;
      }

      .button-public.active {
        @apply bg-primary text-on-primary;
      }

      .button-private.active {
        @apply bg-secondary text-on-secondary;
      }

      .bbs-submit {
        @apply px-2 py-0 text-sm font-bold drop-shadow-none;
        @apply max-md:ml-auto;
        @apply md:ml-10;

        &.public {
          @apply border-on-primary-container bg-primary-container text-on-primary-container hover:bg-primary hover:text-on-primary;
        }

        &.private {
          @apply border-on-secondary-container bg-secondary-container text-on-secondary-container hover:bg-secondary hover:text-on-secondary;
        }
      }
    }
  }

  .viewer {
    @apply my-4;
    @apply px-4;
    @apply md:px-8;

    .viewer-title {
      @apply mb-2 w-fit rounded-lg bg-surface-variant px-2 py-1 text-left text-sm font-bold text-on-surface-variant;
    }

    .no-post {
      @apply my-5 text-on-surface-variant;
    }

    .post {
      @apply mb-2 overflow-hidden rounded-lg border;

      .post-header {
        @apply flex w-full flex-wrap px-4 py-1;

        .post-turn {
          @apply text-left;
          @apply max-md:mr-auto;
          @apply md:my-auto md:mr-3 md:pr-3;

          .turn-title {
            @apply text-xs;
          }

          .turn-num {
            @apply font-bold;
          }
        }

        .post-profile {
          @apply mr-2 max-md:order-2 max-md:my-1 max-md:w-full max-md:min-w-0 max-md:text-center;
          @apply md:mr-5 md:inline-flex;

          .post-island-owner {
            @apply mr-1 inline font-bold text-on-surface;
          }

          .post-island-name {
            @apply mt-auto inline text-sm font-bold text-on-link;
          }
        }

        .post-badge {
          @apply mr-2 inline-flex items-center rounded-full px-2 text-xs font-bold leading-none;

          &.owner {
            @apply bg-primary text-on-primary;
          }

          &.private {
            @apply bg-secondary text-on-secondary;
          }

          .badge-text {
            @apply m-auto block;
          }
        }

        .delete-button {
          @apply cursor-pointer rounded-md bg-error px-1 dark:bg-on-error;
          @apply md:ml-auto;

          .icon {
            @apply text-sm text-on-error dark:text-error;
          }
        }
      }

      .post-contents {
        @apply px-8 py-2 text-left;

        .post-delete-error {
          @apply text-right text-xs font-bold text-error;
        }

        .post-comment {
          @apply text-sm;
        }
      }
    }

    .post-deleted,
    .post-private-hidden {
      @apply mx-auto mb-3 w-fit rounded-lg px-3 py-1 text-center text-sm;
    }

    .post-deleted {
      @apply bg-error-container text-error;
    }

    .post-private-hidden {
      @apply bg-alert-container text-alert;
    }
  }
}
</style>
