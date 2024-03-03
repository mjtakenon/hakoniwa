<template>
  <TresCanvas v-bind="gl" id="island-canvas">
    <IslandViewer />
  </TresCanvas>
</template>

<script setup lang="ts">
import { NoToneMapping, SRGBColorSpace, VSMShadowMap } from 'three'
import { TresCanvas } from '@tresjs/core'
import { reactive } from 'vue'
import IslandViewer from '$vue/components/islands/Viewer/IslandViewer.vue'
import { useIslandViewerStore } from '$store/IslandViewerStore.js'

const gl = reactive({
  clearColor: '#888888',
  shadows: true,
  alpha: true,
  shadowMapType: VSMShadowMap,
  outputColorSpace: SRGBColorSpace,
  toneMapping: NoToneMapping
})

const store = useIslandViewerStore()
</script>

<style lang="scss" scoped></style>
