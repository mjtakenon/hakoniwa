<template>
  <div class="island-settings">
    <h2 class="subtitle">島名・オーナー名の設定</h2>
    <p class="descriptions">
      島名・オーナー名の設定には一回当たり
      <span class="font-bold text-error">{{ changeIslandNamePrice }}億円</span>
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

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { AjaxResult, ErrorType, RequestStatus } from '$js/entity/Network.js'
import { stringEquals } from '$js/Utils.js'
import { useUserStore } from '$store/UserStore.js'

interface Props {
  changeIslandNamePrice: number
}
const props = defineProps<Props>()

const name = ref('')
const nameError = ref('')
const owner = ref('')
const ownerError = ref('')
const otherError = ref('')
const submitStatus = ref<AjaxResult>({
  status: RequestStatus.None
})

const store = useUserStore()

const isFundsEnough = computed(() => {
  return store.user.status.funds >= props.changeIslandNamePrice || submitStatus.value.error === ErrorType.LackOfFunds
})

const isSuccess = computed(() => {
  return submitStatus.value.status === RequestStatus.Success
})

const isSubmitting = computed(() => {
  return submitStatus.value.status === RequestStatus.Updating
})

const hasError = computed(() => {
  return nameError.value !== '' || ownerError.value !== '' || otherError.value !== ''
})

const submitNameChange = async () => {
  checkInputs()
  if (hasError.value) return
  if (isSubmitting.value) return
  submitStatus.value.status = RequestStatus.Updating
  submitStatus.value = await store.patchIslandName(name.value, owner.value)

  console.log(submitStatus.value)

  if (submitStatus.value.status === RequestStatus.Success) return

  if (submitStatus.value.error === ErrorType.LackOfFunds) {
    otherError.value = '資金が不足しているため、更新に失敗しました。'
  } else if (submitStatus.value.error === ErrorType.NotChanged) {
    otherError.value = '名称の変更がなかったため、更新を中止しました。'
    nameError.value = ' '
    ownerError.value = ' '
  } else if (submitStatus.value.error === ErrorType.DuplicatedIslandName) {
    nameError.value = '島名が既に利用されているため、変更できません。'
  } else if (submitStatus.value.error === ErrorType.DuplicatedOwnerName) {
    ownerError.value = 'オーナー名が既に利用されているため、変更できません。'
  } else {
    otherError.value = '不明なエラーが発生しました。'
  }
}

const checkInputs = () => {
  name.value = name.value.trim()
  owner.value = owner.value.trim()
  nameError.value = ''
  ownerError.value = ''
  otherError.value = ''

  if (name.value === '') {
    nameError.value = '島名が入力されていません'
  }
  if (owner.value === '') {
    ownerError.value = 'オーナー名が入力されていません'
  }

  if (stringEquals(name.value, store.user.island.name) && stringEquals(owner.value, store.user.island.owner_name)) {
    otherError.value = '島名・オーナー名ともに変更されていません。更新する場合はどちらかの名称を変更してください。'
    nameError.value = ' '
    ownerError.value = ' '
  }
}

onMounted(() => {
  name.value = store.user.island.name
  owner.value = store.user.island.owner_name
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
