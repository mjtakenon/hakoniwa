<template>
  <canvas ref="canvas"/>
</template>

<script setup lang="ts">
import {AmbientLight, Box3, Camera, Group, Mesh, PerspectiveCamera, Scene, Vector3, WebGLRenderer} from 'three'
import {useGLTF} from '@tresjs/cientos'
import {onMounted, ref, UnwrapRef} from 'vue'
import {CellType, DEFAULT_CELL_SIZE, getCellPath, getCellSubTypes, getCellTypes} from '$entity/Cell.js'
import {useIslandHoverStore} from '$store/IslandHoverStore.js'

const store = useIslandHoverStore()

const width = 32
const height = 32

const scene = new Scene()

const canvas = ref(null)

let models = {}
for (let type of getCellTypes()) {
  models[type] = {}
  for (let subType of getCellSubTypes(type as CellType)) {
    let paths = getCellPath(type as CellType, subType)
    models[type][subType] = []

    for (let path of paths) {
      let model = await useGLTF(path['path'], {draco: true})
      const size = new Box3().setFromObject(model.scene).getSize(new Vector3())
      model.scene.scale.x = DEFAULT_CELL_SIZE / size.x
      model.scene.scale.y = DEFAULT_CELL_SIZE / size.x
      model.scene.scale.z = DEFAULT_CELL_SIZE / size.x
      model.scene.position.y += (size.y * (DEFAULT_CELL_SIZE / size.x) - DEFAULT_CELL_SIZE) / 2

      model.scene.traverse((object: Mesh | Group) => {
        if (object.isMesh) {
          object.material.transparent = true;
          object.material.opacity = path['opacity'] ?? 1;
        }
      })

      models[type][subType].push(model)
    }
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
  const positionMargin = new Vector3(DEFAULT_CELL_SIZE * 4, DEFAULT_CELL_SIZE * 4, DEFAULT_CELL_SIZE * -4)
  const cameraPositionDiff = new Vector3(4, 16, 4)

  for (let type of getCellTypes()) {
    store.cameraLookAt[type] = {}
    store.cameraPositions[type] = {}

    for (let subType of getCellSubTypes(type as CellType)) {
      models[type][subType].forEach((model) => {
        model.scene.position.x = position.x
        model.scene.position.y = position.y + model.scene.position.y
        model.scene.position.z = position.z
        store.cameraLookAt[type][subType] = position.clone()
        store.cameraLookAt[type][subType].y += 4
        store.cameraPositions[type][subType] = position.clone().add(cameraPositionDiff)
        scene.add(model.scene)
      })
      position.add(positionMargin)
    }
  }

  store.changeHoverCellCameraFocus('sea', 'default')

  tick()
})

const tick = () => {
  for (let type of getCellTypes()) {
    for (let subType of getCellSubTypes(type as CellType)) {
      models[type][subType].forEach((model) => {
        model.scene.rotation.y += 0.01
      })
    }
  }
  renderer.render(scene, store.camera)
  requestAnimationFrame(tick)
}
</script>

<style lang="scss" scoped></style>
