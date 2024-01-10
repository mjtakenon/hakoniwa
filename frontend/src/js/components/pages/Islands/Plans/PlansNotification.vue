<template>
  <div>
    <div v-show="store.showNotification" :class="notificationClass">
      <!--            <button class="delete" @click="onClickNotificationClose()"></button>-->
      {{ notificationMessage }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useIslandEditorStore } from '../../../../store/IslandEditorStore.js'

const store = useIslandEditorStore()

const STATUS_PAGE_EXPIRED = 419

const onClickNotificationClose = () => {
  store.showNotification = false
}

const notificationMessage = computed(() => {
  const status = store.planSendingResult
  if (status === STATUS_PAGE_EXPIRED)
    return (
      'セッションの有効期限が切れているため、ページを再読み込みしてください。解決しない場合は、管理者にご連絡ください。(status: ' +
      status +
      ')'
    )

  if (status >= 200 && status < 300) {
    return ''
  }
  if (status >= 300 && status < 500) {
    return (
      '不明なエラーが発生しました。時間をおいて再度お試しください。解決しない場合は、管理者にご連絡ください。(status: ' +
      status +
      ')'
    )
  }
  return (
    'サーバーエラーが発生しました。時間をおいて再度お試しください。解決しない場合は、管理者にご連絡ください。(status: ' +
    status +
    ')'
  )
})

const notificationClass = computed(() => {
  return [
    // 'notification',
    store.planSendingResult >= 200 && store.planSendingResult < 300 ? 'has-text-success' : 'has-text-danger'
  ]
})
</script>

<style lang="scss" scoped></style>
