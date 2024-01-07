<template>
  <canvas ref="canvas" />
</template>

<script setup lang="ts">
import { AmbientLight, Box3, Camera, PerspectiveCamera, Scene, Vector3, WebGLRenderer } from 'three'
import { useMainStore } from '../../../store/MainStore'
import { useGLTF } from '@tresjs/cientos'
import { onMounted, ref, UnwrapRef } from 'vue'

const store = useMainStore()

const width = 32
const height = 32

const scene = new Scene()

const canvas = ref(null)

let models = {}
for (let type in store.getCells) {
  let model = await useGLTF(store.getCells[type].path, { draco: true })
  const size = new Box3().setFromObject(model.scene).getSize(new Vector3())
  model.scene.scale.x = store.cellSize / size.x
  model.scene.scale.y = store.cellSize / size.x
  model.scene.scale.z = store.cellSize / size.x
  model.scene.position.y += (size.y * (store.cellSize / size.x) - store.cellSize) / 2
  models[type] = model
}

const light = new AmbientLight(0xffffff, 3)

store.hoverCellCamera = new PerspectiveCamera(40, width / height) as UnwrapRef<Camera>

let renderer: WebGLRenderer | null = null

onMounted(() => {
  renderer = new WebGLRenderer({
    canvas: canvas.value
  })

  renderer.setPixelRatio(window.devicePixelRatio)
  renderer.setSize(width, height)
  renderer.setClearColor(0x000000, 0)

  light.position.set(0, 0, 0).normalize()
  scene.add(light)

  let position = new Vector3(0, 0, 0)
  const positionMargin = new Vector3(store.cellSize * 4, store.cellSize * 4, store.cellSize * -4)
  const cameraPositionDiff = new Vector3(8, 12, 12)

  for (let type in models) {
    models[type].scene.position.x = position.x
    models[type].scene.position.y = position.y
    models[type].scene.position.z = position.z
    store.hoverCellCameraLookAt[type] = position.clone()
    store.hoverCellCameraPositions[type] = position.clone().add(cameraPositionDiff)

    position.add(positionMargin)
    scene.add(models[type].scene)
  }

  store.changeHoverCellCameraFocus('sea')

  tick()
})

const tick = () => {
  for (let type in models) {
    models[type].scene.rotation.y += 0.01
  }
  renderer.render(scene, store.hoverCellCamera)
  requestAnimationFrame(tick)
}
</script>

<style lang="scss" scoped></style>
