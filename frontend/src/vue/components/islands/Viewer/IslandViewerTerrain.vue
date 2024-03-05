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
            0,
            cell.data.point.y * DEFAULT_CELL_SIZE
          ] as Vector3
        "
        :scale="models[cell.type][cell.data.sub_type ?? 'default'].scene.scale.x"
        :cell="cell"
        :scene="models[cell.type][cell.data.sub_type ?? 'default'].scene.clone()"/>
    </template>
  </TresGroup>
</template>

<script async setup lang="ts">
import {Box3, Group, Mesh, Vector3} from 'three'
import {useGLTF} from '@tresjs/cientos'
import IslandViewerCell from './IslandViewerCell.vue'
import {CellType, DEFAULT_CELL_SIZE, getCellPath, getCellSubTypes, getCellTypes} from '$entity/Cell.js'
import {useIslandViewerStore} from '$store/IslandViewerStore.js'

const store = useIslandViewerStore()

let models = {}

for (let type of getCellTypes()) {
  models[type] = {}
  for (let subType of getCellSubTypes(type as CellType)) {
    let paths = getCellPath(type as CellType, subType)

    for (let path of paths) {
      let model = await useGLTF(path['path'], {draco: true})
      const size = new Box3().setFromObject(model.scene).getSize(new Vector3())
      model.scene.scale.x = DEFAULT_CELL_SIZE / size.x
      model.scene.scale.y = DEFAULT_CELL_SIZE / size.x
      model.scene.scale.z = DEFAULT_CELL_SIZE / size.x
      model.scene.children[0].position.y += (size.y * (DEFAULT_CELL_SIZE / size.x) - DEFAULT_CELL_SIZE) / 2

      model.scene.traverse((object: Mesh | Group) => {
        if (object.isMesh) {
          object.material.transparent = true;
          object.material.opacity = path['opacity'] ?? 1;
        }
      })

      models[type][subType] ? models[type][subType].scene.children.push(model.scene.children[0]) : models[type][subType] = model
    }
  }
}
</script>

<style lang="scss" scoped></style>
