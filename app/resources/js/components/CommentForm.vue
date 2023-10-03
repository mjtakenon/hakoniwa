<template>
<div id="comment-form">
    <div class="comment-header">
        <div class="comment-header-title">
            コメント入力
        </div>
        <div v-if="request.status === RequestStatus.Updating"
             class="comment-update"
        >
            <span class="updating-spin"></span>
            <span class="updating">更新中...</span>
        </div>
        <div v-else-if="request.status === RequestStatus.Success"
             class="comment-update"
        >
            <span class="success animate-fadeout-3s">更新完了</span>
        </div>
        <div v-else-if="request.status === RequestStatus.Failed"
             class="comment-update"
        >
            <span v-if="request.error === ErrorType.TooManyRequests" class="failed">
                更新失敗 時間を開けてお試しください
            </span>
            <span v-else class="failed">
                更新失敗
            </span>
        </div>
    </div>
    <div class="comment-body">
        <input id="comment-input" type="text" name="comment-name" maxlength="128" minlength="0"
               v-model="comment"
               @blur="submitComment"
               v-on:keydown.enter="submitComment"
               ref="input"
        >
    </div>
</div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import {useMainStore} from "../store/MainStore";
import {AjaxResult, ErrorType, RequestStatus} from "../store/Entity/Network";
import {stringEquals} from "../Utils";

export default defineComponent({
    computed: {
        ErrorType() {
            return ErrorType
        },
        RequestStatus() {
            return RequestStatus
        },
        UpdateStatus() {
            return RequestStatus
        }
    },
    data() {
        return {
            comment: "",
            request: { status: RequestStatus.None } as AjaxResult,
        }
    },
    setup() {
        const store = useMainStore();
        return {store}
    },
    mounted() {
        this.comment = this.store.island.comment;
    },
    methods: {
        async submitComment() {
            (this.$refs.input as HTMLElement).blur();
            if(stringEquals(this.comment, this.store.island.comment)) return;
            if(this.request.status === RequestStatus.Updating) return;

            this.request.status = RequestStatus.Updating;
            this.request = await this.store.postComment(this.comment);
        }
    }
});
</script>

<style scoped lang="scss">
#comment-form {
    // general
    @apply w-full my-4 text-left;
    // desktop
    @apply md:px-4;

    .comment-header {
        @apply flex justify-between;

        .comment-header-title {
            @apply inline-block text-left bg-surface-variant text-on-surface-variant pt-1 px-4 text-sm;
            @apply rounded-tr-2xl;
            @apply md:rounded-t-2xl;
        }

        .comment-update {
            @apply flex items-center text-right pr-2;

            .updating {
                @apply text-xs text-primary;
            }

            .updating-spin {
                @apply inline-block animate-spin m-1 w-3 h-3 border-2 border-primary rounded-full border-t-transparent;
            }

            .success {
                @apply text-xs text-primary;
            }

            .failed {
                @apply text-xs text-error;
            }
        }
    }

    .comment-body {
        @apply bg-surface-variant -mt-0.5 p-2;
        @apply md:rounded-tr-xl md:rounded-bl-xl md:rounded-br-xl;

        #comment-input {
            @apply w-full bg-surface-variant text-on-surface-variant p-1 border border-background focus:outline-none rounded;
            @apply focus:bg-background focus:text-on-background dark:focus:bg-on-surface-variant dark:focus:text-surface-variant
        }
    }
}
</style>
