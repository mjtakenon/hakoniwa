<template></template>
<script setup lang="ts">
import { onMounted, reactive } from 'vue'
import { useTresContext } from '@tresjs/core'
import { useCameraStore } from '$store/CameraControlsStore.js'

const cameraControlsState = reactive({
  minDistance: 20,
  maxDistance: 200,
  maxPolarAngle: Math.PI / 2
})

const store = useCameraStore()

onMounted(() => {
  const context = useTresContext()
  store.camera = context.camera.value
  // console.log(props.target)
})
</script>
