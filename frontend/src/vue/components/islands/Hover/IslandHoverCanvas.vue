<template>
  <canvas ref="canvas"/>
</template>

<script setup lang="ts">
import {AmbientLight, Camera, Group, PerspectiveCamera, Scene, Vector3, WebGLRenderer} from 'three'
import {useGLTF} from '@tresjs/cientos'
import {onMounted, ref, UnwrapRef} from 'vue'
import {CELL_SIZE_X, CellType, getCellModels, getCellSubTypes, getCellTypes} from '$entity/Cell.js'
import {useIslandHoverStore} from '$store/IslandHoverStore.js'

const store = useIslandHoverStore()

const width = 32
const height = 32

const scene = new Scene()

const canvas = ref(null)

let nodes = {}
let gltfResult = await useGLTF('/img/hakoniwa/glb/models.glb', {draco: true})

for (let child of gltfResult.scene.children) {
  nodes[child.name] = child
}

let models = {}

for (let type of getCellTypes()) {
  models[type] = {}

  for (let subType of getCellSubTypes(type)) {
    let group = new Group();

    for (let models of getCellModels(type, subType)) {
      let n = nodes[models.model].clone(false)
      n.material.opacity = models.opacity ?? 1
      if (n.material.opacity < 1) {
        n.material.transparent = true;
      }
      group.add(n)
    }

    models[type][subType] = group
  }
}

const light = new AmbientLight(0xffffff, 3)

store.camera = new PerspectiveCamera(40, width / height) as UnwrapRef<Camera>

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
  const positionMargin = new Vector3(CELL_SIZE_X * 4, CELL_SIZE_X * 4, CELL_SIZE_X * -4)
  const cameraPositionDiff = new Vector3(CELL_SIZE_X, CELL_SIZE_X * 1.5, CELL_SIZE_X)

  for (let type of getCellTypes()) {
    store.cameraLookAt[type] = {}
    store.cameraPositions[type] = {}

    for (let subType of getCellSubTypes(type as CellType)) {
      models[type][subType].children[0].position.x = position.x
      models[type][subType].children[0].position.y = position.y + models[type][subType].position.y
      models[type][subType].children[0].position.z = position.z
      store.cameraLookAt[type][subType] = position.clone()
      store.cameraLookAt[type][subType].y += 1
      store.cameraPositions[type][subType] = position.clone().add(cameraPositionDiff)
      scene.add(models[type][subType])
      position.add(positionMargin)
    }
  }

  store.changeHoverCellCameraFocus('sea', 'default')

  tick()
})

const tick = () => {
  for (let type of getCellTypes()) {
    for (let subType of getCellSubTypes(type as CellType)) {
      models[type][subType].children[0].rotation.y += 0.01
    }
  }
  renderer.render(scene, store.camera)
  requestAnimationFrame(tick)
}
</script>

<style lang="scss" scoped></style>
