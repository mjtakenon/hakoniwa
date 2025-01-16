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
        :group="cellModels[cell.type][cell.data.sub_type ?? 'default']"/>
    </template>
    <template v-for="edge of store.terrain.edges">
      <IslandViewerEdge
        :position="[
          (edge.data.point.x * ((CELL_SIZE_X + EDGE_WIDTH_X) * DEFAULT_MODEL_SCALE)) + (((edge.data.point.y + 1) % 2) * ((CELL_SIZE_X + EDGE_WIDTH_X) / 2) * DEFAULT_MODEL_SCALE),
          0,
          edge.data.point.y * (CELL_SIZE_Y + EDGE_WIDTH_Y) * DEFAULT_MODEL_SCALE
        ]"
        :edge="edge"
        :group="edgeModels[edge.type][edge.data.sub_type ?? 'default']"/>
    </template>
  </TresGroup>
</template>

<script async setup lang="ts">
import {Group, Vector3} from 'three'
import {useGLTF} from '@tresjs/cientos'
import IslandViewerCell from './IslandViewerCell.vue'
import {
  CELL_SIZE_X,
  CELL_SIZE_Y,
  DEFAULT_MODEL_SCALE,
  getCellModels,
  getCellSubTypes,
  getCellTypes
} from '$entity/Cell.js'
import {useIslandViewerStore} from '$store/IslandViewerStore.js'
import {EDGE_WIDTH_X, EDGE_WIDTH_Y, getEdgeModels, getEdgeSubTypes, getEdgeTypes,} from "$entity/Edge.js";
import IslandViewerEdge from "$vue/components/islands/Viewer/IslandViewerEdge.vue";

const store = useIslandViewerStore()

let nodes = {}
let gltfResult = await useGLTF('/img/hakoniwa/glb/models.glb', {draco: true})

for (let child of gltfResult.scene.children) {
  nodes[child.name] = child
}

let cellModels = {}

for (let type of getCellTypes()) {
  cellModels[type] = {}

  for (let subType of getCellSubTypes(type)) {
    let group = new Group();

    for (let models of getCellModels(type, subType)) {
      // TODO: InstancedMeshを利用する
      let node = nodes[models.model].clone()
      node.userData.elevation_multiply = models.elevation_multiply ?? 1
      node.userData.opacity = models.opacity ?? 1
      group.children.push(node)
    }

    cellModels[type][subType] = group
  }
}

let edgeModels = {}

for (let type of getEdgeTypes()) {
  edgeModels[type] = {}

  for (let subType of getEdgeSubTypes(type)) {
    let group = new Group();

    for (let models of getEdgeModels(type, subType)) {
      let node = nodes[models.model].clone()
      node.userData.elevation_multiply = models.elevation_multiply ?? 1
      node.userData.opacity = models.opacity ?? 1
      group.children.push(node)
    }

    edgeModels[type][subType] = group
  }
}

</script>

<style lang="scss" scoped></style>
