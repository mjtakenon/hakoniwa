<template>
  <TresGroup
    :position="
      [
        -DEFAULT_CELL_SIZE * Math.floor(store.hakoniwa.width / 2),
        0,
        -DEFAULT_CELL_SIZE * Math.floor(store.hakoniwa.height / 2)
      ] as Vector3
    ">
    <template v-for="cell of store.terrain.cells">
      <IslandViewerCell
        :position="
          [
            cell.data.point.x * DEFAULT_CELL_SIZE + ((((cell.data.point.y + 1) % 2) - 1) * DEFAULT_CELL_SIZE) / 2,
            models[cell.type][cell.data.sub_type ?? 'default'].scene.position.y,
            cell.data.point.y * DEFAULT_CELL_SIZE
          ] as Vector3
        "
        :scale="models[cell.type][cell.data.sub_type ?? 'default'].scene.scale.x"
        :cell="cell"
        :scene="models[cell.type][cell.data.sub_type ?? 'default'].scene.clone()" />
    </template>
  </TresGroup>
</template>

<script async setup lang="ts">
import { Box3, Vector3 } from 'three'
import { useGLTF } from '@tresjs/cientos'
import IslandViewerCell from './IslandViewerCell.vue'
import { CellType, DEFAULT_CELL_SIZE, getCellPath, getCellSubTypes, getCellTypes } from '$entity/Cell.js'
import { useIslandViewerStore } from '$store/IslandViewerStore.js'

const store = useIslandViewerStore()

let models = {}

for (let type of getCellTypes()) {
  models[type] = {}
  for (let subType of getCellSubTypes(type as CellType)) {
    let model = await useGLTF(getCellPath(type as CellType, subType), { draco: true })
    const size = new Box3().setFromObject(model.scene).getSize(new Vector3())
    model.scene.scale.x = DEFAULT_CELL_SIZE / size.x
    model.scene.scale.y = DEFAULT_CELL_SIZE / size.x
    model.scene.scale.z = DEFAULT_CELL_SIZE / size.x
    model.scene.position.y += (size.y * (DEFAULT_CELL_SIZE / size.x) - DEFAULT_CELL_SIZE) / 2
    models[type][subType] = model
  }
}
</script>

<style lang="scss" scoped></style>
