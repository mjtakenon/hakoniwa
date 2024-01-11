<template>
  <div id="index">
    <h1 class="title">やまにてぃ</h1>

    <TurnViewer :turn="props.turn" :day="props.day" :time="props.time" />

    <h2 class="subtitle">注意事項</h2>
    <InfoBox type="notify">
      現在開発中のため、多くの機能が未実装です。
    </InfoBox>
    <InfoBox type="alert">
      テスト中にデータが消える可能性が高いため、何があっても許せる方のみ登録してください。
    </InfoBox>

    <h2 class="subtitle">遊び方</h2>
    <p>
      TAITOの島育成ゲームソーシャルネットワークサービス「しまにてぃ」の要素を取り入れた平和系箱庭です。 更新は約
      {{ props.updateInterval }} 分毎です。
    </p>
    <p>フルスクラッチで実装しているため、既存の箱庭と仕様が違う点はご容赦ください。連絡いただければ検討します。</p>
    <p>
      ログインは（今のところ）Google連携のみ利用できます。メールアドレスとユーザー名をサーバーで保持するので、気になる人は捨て垢の利用を推奨します（パスワードはこちらでは保持しません）。
    </p>
    <p>不具合・不明点はツイッターからご連絡ください。</p>
    <InfoBox type="error">
      重複登録は禁止です。1人1島でお願いします。
    </InfoBox>

    <h2 class="subtitle mt-20">現在のランキング</h2>
    <template v-if="props.rankingIslands.length > 0">
      <RankingViewer
        v-for="(island, index) in props.rankingIslands"
        :island="island"
        :index="index + 1"
        :key="island.id" />
    </template>
    <p v-else>島が登録されていません</p>

    <LogViewer v-if="props.logs != null" title="最近の出来事" :unparsed-logs="props.logs" />
  </div>
</template>

<script setup lang="ts">
import TurnViewer from '$vue/pages/Index/TurnViewer.vue'
import RankingViewer from '$vue/pages/Index/RankingViewer.vue'
import { IslandWithStatuses } from '$entity/Island.js'
import LogViewer from '$vue/components/islands/common/LogViewer.vue'
import { LogProps } from '$entity/Log.js'
import InfoBox from "$vue/components/ui/InfoBox.vue";

type Props = {
  turn: string
  day: string
  time: string
  updateInterval: string
  rankingIslands: IslandWithStatuses[]
  logs: LogProps[]
}

const props = defineProps<Props>()
</script>

<style scoped lang="scss">
#index {
  @apply mx-auto max-w-[1000px] px-3;

  .title {
    @apply mb-4 mt-6 border-b pb-1 text-2xl font-black;
  }

  .subtitle {
    @apply mb-2 mt-6 border-b text-lg;
  }
}
</style>
