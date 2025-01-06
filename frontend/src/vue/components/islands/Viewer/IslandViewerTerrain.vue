<template>
  <TresGroup
    :position="
      [
        -CELL_SIZE_X * Math.floor(store.hakoniwa.width / 2),
        0,
        -CELL_SIZE_X * Math.floor(store.hakoniwa.height / 2)
      ] as Vector3
    ">
    <template v-for="cell of store.terrain.cells">
      <IslandViewerCell
        :position="[
          (cell.data.point.x * ((CELL_SIZE_X + EDGE_WIDTH_X) * DEFAULT_MODEL_SCALE)) + (((cell.data.point.y + 1) % 2) * ((CELL_SIZE_X + EDGE_WIDTH_X) / 2) * DEFAULT_MODEL_SCALE),
          0,
          cell.data.point.y * ((CELL_SIZE_Y + EDGE_WIDTH_Y) * DEFAULT_MODEL_SCALE),
        ]"
        :cell="cell"
        :scene="cellModels[cell.type][cell.data.sub_type ?? 'default'].scene.clone()"/>
    </template>
    <template v-for="edge of store.terrain.edges">
      <IslandViewerEdge
        :position="[
          (edge.data.point.x * ((CELL_SIZE_X + EDGE_WIDTH_X) * DEFAULT_MODEL_SCALE)) + (((edge.data.point.y + 1) % 2) * ((CELL_SIZE_X + EDGE_WIDTH_X) / 2) * DEFAULT_MODEL_SCALE),
          0,
          edge.data.point.y * (CELL_SIZE_Y + EDGE_WIDTH_Y) * DEFAULT_MODEL_SCALE
        ]"
        :scale="edgeModels[edge.type]['default'].scene.scale.toArray()"
        :edge="edge"
        :scene="edgeModels[edge.type]['default'].scene.clone()"/>
    </template>
  </TresGroup>
</template>

<script async setup lang="ts">
import {Box3, Group, Mesh, Vector3} from 'three'
import {useGLTF} from '@tresjs/cientos'
import IslandViewerCell from './IslandViewerCell.vue'
import {
  CellType,
  CELL_SIZE_X,
  getCellPath,
  getCellSubTypes,
  getCellTypes,
  DEFAULT_MODEL_SCALE,
  CELL_SIZE_Y
} from '$entity/Cell.js'
import {useIslandViewerStore} from '$store/IslandViewerStore.js'
import {
  EDGE_WIDTH_X,
  EDGE_WIDTH_Y,
  EdgeType,
  getEdgePath,
  getEdgeSubTypes,
  getEdgeTypes,
} from "$entity/Edge.js";
import IslandViewerEdge from "$vue/components/islands/Viewer/IslandViewerEdge.vue";

const store = useIslandViewerStore()

let cellModels = {}
let edgeModels = {}

for (let type of getCellTypes()) {
  cellModels[type] = {}
  for (let subType of getCellSubTypes(type as CellType)) {
    let paths = getCellPath(type as CellType, subType)

    for (let path of paths) {
      let model = await useGLTF(path['path'], {draco: true})
      // const size = new Box3().setFromObject(model.scene).getSize(new Vector3())
      // model.scene.children[0].position.y += (size.y * DEFAULT_MODEL_SCALE - CELL_SIZE_X) / 2

      model.scene.traverse((object: Mesh | Group) => {
        if (object.isMesh) {
          object.material.transparent = true;
          object.material.opacity = path['opacity'] ?? 1;
        }
      })

      cellModels[type][subType] ? cellModels[type][subType].scene.children.push(model.scene.children[0]) : cellModels[type][subType] = model
    }
  }
}

for (let type of getEdgeTypes()) {
  edgeModels[type] = {}
  for (let subType of getEdgeSubTypes(type as EdgeType)) {
    let paths = getEdgePath(type as EdgeType, subType)

    for (let path of paths) {
      let model = await useGLTF(path['path'], {draco: true})
      // const size = new Box3().setFromObject(model.scene).getSize(new Vector3())
      // model.scene.children[0].position.y += (size.y * DEFAULT_MODEL_SCALE - CELL_SIZE_X) / 2

      model.scene.traverse((object: Mesh | Group) => {
        if (object.isMesh) {
          object.material.transparent = true;
          object.material.opacity = path['opacity'] ?? 1;
        }
      })

      edgeModels[type][subType] ? edgeModels[type][subType].scene.children.push(model.scene.children[0]) : edgeModels[type][subType] = model
    }
  }
}
</script>

<style lang="scss" scoped></style>
