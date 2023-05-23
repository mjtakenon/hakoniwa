<template>
    <div class="island-settings">
        <h2 class="subtitle">島名・オーナー名の設定</h2>
        <p class="descriptions">
            島名・オーナー名の設定には一回当たり
            <span class="text-error font-bold">{{ change_island_name_price }}億円</span>
            がかかります。不足している場合は変更することができません。
        </p>
        <p v-if="!isFundsEnough" class="lack-of-funds">
            資金が不足しているため、再設定ができません。
        </p>
        <div class="input-wrapper">
            <div class="input-header">
                <span>島の名前（最大32文字）</span>
                <span v-if="nameError !== ''" class="input-error">{{ nameError }}</span>
            </div>
            <input type="text" name="island-name" maxlength="32" minlength="1"
                   v-model="name" @blur="checkInputs"
                   :disabled="isSubmitting || !isFundsEnough"
                   class="input-form" :class="{'error': nameError !== ''}"
            >
        </div>
        <div class="input-wrapper">
            <div class="input-header">
                <span>オーナー名（最大32文字）</span>
                <span v-if="ownerError !== ''" class="input-error">{{ ownerError }}</span>
            </div>
            <input type="text" name="island-owner" maxlength="32" minlength="1"
                   v-model="owner" @blur="checkInputs"
                   :disabled="isSubmitting || !isFundsEnough"
                   class="input-form" :class="{'error': ownerError !== ''}"
            >
        </div>
        <div v-if="otherError !== ''" class="other-error">{{ otherError }}</div>
        <div v-if="submitStatus === UpdateStatus.Success" class="success">更新に成功しました。</div>
        <button
            class="submit button-primary"
            @click="submitNameChange"
            :disabled="isSubmitting || !isFundsEnough"
        >
            <span v-if="!isSubmitting">送信</span>
            <span v-else class="button-circle">
                <span class="button-circle-spin"></span>
            </span>
        </button>
    </div>
</template>

<script lang="ts">
import {defineComponent} from 'vue'
import {UpdateStatus} from "../store/Entity/Network";
import {useMainStore} from "../store/MainStore";
import {stringEquals} from "../Utils";

export default defineComponent({
    data() {
        return {
            name: "",
            nameError: "",
            owner: "",
            ownerError: "",
            otherError: "",
            submitStatus: UpdateStatus.None as UpdateStatus,
        }
    },
    setup() {
        const store = useMainStore();
        return {store};
    },
    mounted() {
        this.name = this.store.island.name;
        this.owner = this.store.island.owner_name;
    },
    computed: {
        UpdateStatus() {
            return UpdateStatus
        },
        isFundsEnough() {
            return this.store.status.funds >= this.change_island_name_price
                || this.store.patchIslandNameError === "lack_of_funds";
        },
        isSubmitting() {
            return this.submitStatus === UpdateStatus.Updating;
        }
    },
    methods: {
        async submitNameChange() {
            if (!this.checkOnSubmit()) return;
            if (this.isSubmitting) return;
            this.submitStatus = UpdateStatus.Updating;
            const result = await this.store.patchIslandName(this.name, this.owner);
            if (result) {
                this.submitStatus = UpdateStatus.Success;
            } else {
                this.submitStatus = UpdateStatus.Failed;
                if (this.store.patchIslandNameError === "lack_of_funds") {
                    this.otherError = "資金が不足しているため、更新に失敗しました。"
                }
                if (this.store.patchIslandNameError === "not_changed") {
                    this.otherError = "名称の変更がなかったため、更新を中止しました。"
                    this.nameError = " ";
                    this.ownerError = " ";
                }
            }
        },
        checkInputs() {
            this.nameError = "";
            this.ownerError = "";
            this.otherError = "";

            if (this.name === "") {
                this.nameError = "島名が入力されていません";
            }
            if (this.owner === "") {
                this.ownerError = "オーナー名が入力されていません";
            }
        },
        checkOnSubmit() {
            this.checkInputs();
            if (stringEquals(this.name, this.store.island.name) && stringEquals(this.owner, this.store.island.owner_name)) {
                this.otherError = "島名・オーナー名ともに変更されていません。更新する場合はどちらかの名称を変更してください。"
                this.nameError = " ";
                this.ownerError = " ";
            }

            return (this.nameError === "" && this.ownerError === "" && this.otherError === "")
        }
    },
    props: {
        change_island_name_price: {
            required: true,
            type: Number,
        }
    }
})
</script>

<style scoped lang="scss">
.island-settings {
    // general
    @apply w-full bg-surface-variant rounded-md mb-6;
    // sp
    @apply p-2;
    // desktop
    @apply md:py-4 md:px-6;

    .subtitle {
        @apply my-2 border-l-4 border-on-background pl-6 font-bold;
    }

    .descriptions {
        @apply mt-4 mx-2 md:mx-6;
    }

    .lack-of-funds {
        @apply mx-6 my-4 text-error font-bold text-center;
    }

    .input-wrapper:first-of-type {
        @apply mt-8;
    }

    .input-wrapper {
        @apply mt-2　text-on-surface-variant;

        .input-header {
            @apply flex items-end justify-between;

            .input-error {
                @apply -mt-1 text-xs md:text-sm text-error px-3;
            }
        }

        .input-form {
            @apply w-full bg-background text-on-background p-1 outline-1 outline-primary rounded drop-shadow-md;
            @apply disabled:opacity-40;
            // sp
            @apply w-full mb-2 px-2;
            // desktop
            @apply md:px-4;

            &.error {
                @apply border border-error;
            }
        }
    }

    .other-error {
        @apply w-full text-center text-error font-bold;
        // sp
        @apply text-xs;
        // desktop
        @apply md:text-sm;
    }

    .success {
        @apply text-primary text-center mt-2;
    }

    .submit {
        @apply block mt-5 mx-auto;
        @apply disabled:hover:bg-primary disabled:text-on-primary;
        // sp
        @apply w-1/2;
        // desktop
        @apply md:w-1/6;
    }

    .button-circle {
        @apply flex justify-center;

        .button-circle-spin {
            @apply block animate-spin m-1 w-4 h-4 border-2 border-on-primary rounded-full border-t-transparent;
        }
    }
}
</style>