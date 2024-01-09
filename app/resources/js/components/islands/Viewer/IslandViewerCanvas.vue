<template>
  <TresGroup
    :position="
      [
        -DEFAULT_CELL_SIZE * Math.floor(store.hakoniwa.width / 2),
        0,
        -DEFAULT_CELL_SIZE * Math.floor(store.hakoniwa.height / 2)
      ] as Vector3
    ">
    <template v-for="terrain of store.terrains">
      <IslandViewerCell
        :position="
          [
            terrain.data.point.x * DEFAULT_CELL_SIZE + ((((terrain.data.point.y + 1) % 2) - 1) * DEFAULT_CELL_SIZE) / 2,
            models[terrain.type].scene.position.y,
            terrain.data.point.y * DEFAULT_CELL_SIZE
          ] as Vector3
        "
        :scale="models[terrain.type].scene.scale.x"
        :terrain="terrain"
        :scene="models[terrain.type].scene.clone()"></IslandViewerCell>
    </template>
  </TresGroup>
</template>

<script async setup lang="ts">
import { Box3, Vector3 } from 'three'
import { useGLTF } from '@tresjs/cientos'
import IslandViewerCell from './IslandViewerCell.vue'
import { DEFAULT_CELL_SIZE, getCells } from '../../../store/Entity/Cell.js'
import { useIslandViewerStore } from '../../../store/IslandViewerStore.js'

const store = useIslandViewerStore()

let models = {}

for (let type in getCells()) {
  let model = await useGLTF(getCells()[type].path, { draco: true })
  const size = new Box3().setFromObject(model.scene).getSize(new Vector3())
  model.scene.scale.x = DEFAULT_CELL_SIZE / size.x
  model.scene.scale.y = DEFAULT_CELL_SIZE / size.x
  model.scene.scale.z = DEFAULT_CELL_SIZE / size.x
  model.scene.position.y += (size.y * (DEFAULT_CELL_SIZE / size.x) - DEFAULT_CELL_SIZE) / 2
  models[type] = model
}
</script>

<style lang="scss" scoped></style>
