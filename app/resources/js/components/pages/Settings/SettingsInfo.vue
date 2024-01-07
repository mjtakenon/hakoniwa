<template>
  <div class="island-settings">
    <h2 class="subtitle">島名・オーナー名の設定</h2>
    <p class="descriptions">
      島名・オーナー名の設定には一回当たり
      <span class="font-bold text-error">{{ change_island_name_price }}億円</span>
      がかかります。不足している場合は変更することができません。
    </p>
    <p v-if="!isFundsEnough" class="lack-of-funds">資金が不足しているため、再設定ができません。</p>
    <div class="input-wrapper">
      <div class="input-header">
        <span>島の名前（最大32文字）</span>
        <span v-if="nameError !== ''" class="input-error">{{ nameError }}</span>
      </div>
      <div class="relative mb-2 w-full">
        <div class="icons">
          <span>島</span>
        </div>
        <input
          type="text"
          name="island-name"
          maxlength="32"
          minlength="1"
          v-model="name"
          @blur="checkInputs"
          :disabled="isSubmitting || !isFundsEnough"
          class="input-form"
          :class="{ error: nameError !== '' }" />
      </div>
    </div>
    <div class="input-wrapper">
      <div class="input-header">
        <span>オーナー名（最大32文字）</span>
        <span v-if="ownerError !== ''" class="input-error">{{ ownerError }}</span>
      </div>
      <input
        type="text"
        name="island-owner"
        maxlength="32"
        minlength="1"
        v-model="owner"
        @blur="checkInputs"
        :disabled="isSubmitting || !isFundsEnough"
        class="input-form"
        :class="{ error: ownerError !== '' }" />
    </div>
    <div v-if="otherError !== ''" class="other-error">{{ otherError }}</div>
    <div v-if="isSuccess" class="success">更新に成功しました。</div>
    <button
      class="submit button-primary"
      @click="submitNameChange"
      :disabled="isSubmitting || !isFundsEnough || hasError">
      <span v-if="!isSubmitting">送信</span>
      <span v-else class="button-circle">
        <span class="button-circle-spin"></span>
      </span>
    </button>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { AjaxResult, ErrorType, RequestStatus } from '../../../store/Entity/Network'
import { useMainStore } from '../../../store/MainStore'
import { stringEquals } from '../../../Utils'

export default defineComponent({
  data() {
    return {
      name: '',
      nameError: '',
      owner: '',
      ownerError: '',
      otherError: '',
      submitStatus: { status: RequestStatus.None } as AjaxResult
    }
  },
  setup() {
    const store = useMainStore()
    return { store }
  },
  mounted() {
    this.name = this.store.island.name
    this.owner = this.store.island.owner_name
  },
  computed: {
    isFundsEnough() {
      return (
        this.store.status.funds >= this.change_island_name_price || this.submitStatus.error === ErrorType.LackOfFunds
      )
    },
    isSuccess() {
      return this.submitStatus.status === RequestStatus.Success
    },
    isSubmitting() {
      return this.submitStatus.status === RequestStatus.Updating
    },
    hasError() {
      return this.nameError !== '' || this.ownerError !== '' || this.otherError !== ''
    }
  },
  methods: {
    async submitNameChange() {
      this.checkInputs()
      if (this.hasError) return
      if (this.isSubmitting) return
      this.submitStatus.status = RequestStatus.Updating
      this.submitStatus = await this.store.patchIslandName(this.name, this.owner)

      if (this.submitStatus.status === RequestStatus.Success) return

      if (this.submitStatus.error === ErrorType.LackOfFunds) {
        this.otherError = '資金が不足しているため、更新に失敗しました。'
      } else if (this.submitStatus.error === ErrorType.NotChanged) {
        this.otherError = '名称の変更がなかったため、更新を中止しました。'
        this.nameError = ' '
        this.ownerError = ' '
      } else if (this.submitStatus.error === ErrorType.DuplicatedIslandName) {
        this.nameError = '島名が既に利用されているため、変更できません。'
      } else if (this.submitStatus.error === ErrorType.DuplicatedOwnerName) {
        this.ownerError = 'オーナー名が既に利用されているため、変更できません。'
      } else {
        this.otherError = '不明なエラーが発生しました。'
      }
    },
    checkInputs() {
      this.name = this.name.trim()
      this.owner = this.owner.trim()
      this.nameError = ''
      this.ownerError = ''
      this.otherError = ''

      if (this.name === '') {
        this.nameError = '島名が入力されていません'
      }
      if (this.owner === '') {
        this.ownerError = 'オーナー名が入力されていません'
      }

      if (stringEquals(this.name, this.store.island.name) && stringEquals(this.owner, this.store.island.owner_name)) {
        this.otherError = '島名・オーナー名ともに変更されていません。更新する場合はどちらかの名称を変更してください。'
        this.nameError = ' '
        this.ownerError = ' '
      }
    }
  },
  props: {
    change_island_name_price: {
      required: true,
      type: Number
    }
  }
})
</script>

<style scoped lang="scss">
.island-settings {
  // general
  @apply mb-6 w-full rounded-md bg-surface-variant;
  // sp
  @apply p-2;
  // desktop
  @apply md:px-6 md:py-4;

  .subtitle {
    @apply my-2 border-l-4 border-on-background pl-6 font-bold;
  }

  .descriptions {
    @apply mx-2 mt-4 md:mx-6;
  }

  .lack-of-funds {
    @apply mx-6 my-4 text-center font-bold text-error;
  }

  .input-wrapper:first-of-type {
    @apply mt-8;
  }

  .icons {
    @apply pointer-events-none absolute z-10 flex w-full items-center justify-end py-1 pr-3;
  }

  .input-wrapper {
    @apply mt-2　text-on-surface-variant;

    .input-header {
      @apply flex items-end justify-between;

      .input-error {
        @apply -mt-1 px-3 text-xs text-error md:text-sm;
      }
    }

    .input-form {
      @apply w-full rounded bg-background p-1 text-on-background outline-1 outline-primary drop-shadow-md;
      @apply disabled:opacity-40;
      // sp
      @apply w-full px-2;
      // desktop
      @apply md:px-4;

      &.error {
        @apply border border-error;
      }
    }
  }

  .other-error {
    @apply w-full text-center font-bold text-error;
    // sp
    @apply text-xs;
    // desktop
    @apply md:text-sm;
  }

  .success {
    @apply mt-2 text-center text-primary;
  }

  .submit {
    @apply mx-auto mt-5 block;
    @apply disabled:bg-on-surface-variant disabled:text-surface-variant;
    // sp
    @apply w-1/2;
    // desktop
    @apply md:w-1/6;
  }

  .button-circle {
    @apply flex justify-center;

    .button-circle-spin {
      @apply m-1 block h-4 w-4 animate-spin rounded-full border-2 border-on-primary border-t-transparent;
    }
  }
}
</style>
