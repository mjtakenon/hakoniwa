<template>
  <TresMapControls
    v-if="context.renderer.value"
    :args="[context.camera.value, context.renderer.value.domElement]"
    ref="controls" />
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { extend, useTresContext } from '@tresjs/core'
import { MapControls } from 'three/examples/jsm/controls/MapControls.js'

extend({ MapControls })

const context = useTresContext()
const controls = ref<MapControls | null>(null)

onMounted(() => {
  controls.value.maxDistance = 250
  controls.value.minDistance = 20
  controls.value.maxPolarAngle = Math.PI / 2
  controls.value.zoomToCursor = true
  controls.value.zoomSpeed = 2
})
</script>
