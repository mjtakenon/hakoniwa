<template>
<div id="comment-form">
    <div class="comment-header">
        <div class="comment-header-title">
            コメント入力
        </div>
        <div v-if="updateStatus === UpdateStatus.Updating"
             class="comment-update"
        >
            <span class="updating-spin"></span>
            <span class="updating">更新中...</span>
        </div>
        <div v-else-if="updateStatus === UpdateStatus.Success"
             class="comment-update"
        >
            <span class="success animate-fadeout-3s">更新完了</span>
        </div>
        <div v-else-if="updateStatus === UpdateStatus.Failed"
             class="comment-update"
        >
            <span class="failed">更新失敗</span>
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
import {UpdateStatus} from "../store/Entity/Network";

export default defineComponent({
    computed: {
        UpdateStatus() {
            return UpdateStatus
        }
    },
    data() {
        return {
            comment: "",
            isCommentChanged: false,
            updateStatus: UpdateStatus.None as UpdateStatus,
        }
    },
    setup() {
        const store = useMainStore();
        return {store}
    },
    mounted() {
        this.comment = this.store.island.comment;
    },
    watch: {
        comment() {
            this.isCommentChanged = true;
        }
    },
    methods: {
        async submitComment() {
            (this.$refs.input as HTMLElement).blur();
            if (!this.isCommentChanged) return;
            this.isCommentChanged = false;
            this.updateStatus = UpdateStatus.Updating;

            const result = await this.store.postComment(this.comment);

            if (result) {
                this.updateStatus = UpdateStatus.Success;
                console.debug("comment update success");
            } else {
                this.updateStatus = UpdateStatus.Failed;
                console.debug("comment update failed");
            }
        }
    }
});
</script>

<style scoped lang="scss">
#comment-form {
    // general
    @apply w-full my-4 text-left;
    // desktop
    @apply md:px-10;

    .comment-header {
        @apply flex justify-between;

        .comment-header-title {
            @apply inline-block text-left bg-surface-variant text-on-surface-variant pt-1 px-4 rounded-t-2xl text-sm;
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
        @apply bg-surface-variant -mt-0.5 p-2 rounded-tr-xl rounded-bl-xl rounded-br-xl;

        #comment-input {
            @apply w-full bg-surface-variant text-on-surface-variant p-1 border border-background focus:outline-none rounded;
            @apply focus:bg-background focus:text-on-background dark:focus:bg-on-surface-variant dark:focus:text-surface-variant
        }
    }
}
</style>
