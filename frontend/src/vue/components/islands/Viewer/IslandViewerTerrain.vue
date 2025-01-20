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
import {Group, InstancedMesh, Object3D, Vector3} from 'three'
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
import {
  EDGE_WIDTH_X,
  EDGE_WIDTH_Y,
  getEdgeModels,
  getEdgeSubTypes,
  getEdgeTypes,
  getRotation,
  getScale,
} from "$entity/Edge.js";
import IslandViewerEdge from "$vue/components/islands/Viewer/IslandViewerEdge.vue";
import {useTresContext} from "@tresjs/core";
import {onMounted} from "vue";

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
      group.children.push(node)
    }

    edgeModels[type][subType] = group
  }
}

// const edgeCounts = store.terrain.edges.reduce((acc, edge) => {
//   acc[edge.type] = acc[edge.type] || []
//   acc[edge.type][edge.data.sub_type ?? 'default'] = (acc[edge.type][edge.data.sub_type ?? 'default'] || 0) + 1
//   return acc
// }, {} as { [key: string]: number })
//
// let edgeInstancedMeshes = []
//
// for (let edgeType in edgeCounts) {
//   for (let edgeSubType in edgeCounts[edgeType]) {
//     if (edgeCounts[edgeType][edgeSubType] === 0) {
//       continue
//     }
//
//     let tmp = new Object3D()
//     for (let child of edgeModels[edgeType][edgeSubType].children) {
//       const edgeInstancedMesh = new InstancedMesh(child.geometry, child.material, edgeCounts[edgeType][edgeSubType])
//       let i = 0
//       for (let edge of store.terrain.edges) {
//         tmp.position.set(
//           (edge.data.point.x * ((CELL_SIZE_X + EDGE_WIDTH_X) * DEFAULT_MODEL_SCALE)) + (((edge.data.point.y + 1) % 2) * ((CELL_SIZE_X + EDGE_WIDTH_X) / 2) * DEFAULT_MODEL_SCALE),
//           edge.data.elevation * 0.1 * child.userData.elevation_multiply,
//           edge.data.point.y * (CELL_SIZE_Y + EDGE_WIDTH_Y) * DEFAULT_MODEL_SCALE,
//         )
//         tmp.rotation.set(getRotation(edge))
//         tmp.scale.set(getScale(edge))
//         tmp.updateMatrix()
//         edgeInstancedMesh.setMatrixAt(i++, tmp.matrix)
//       }
//
//       edgeInstancedMeshes.push(edgeInstancedMesh)
//     }
//   }
// }
//
// const context = useTresContext()
//
// onMounted(() => {
//   for (let edgeInstancedMesh of edgeInstancedMeshes) {
//     context.scene.value.add(edgeInstancedMesh)
//   }
// })

</script>

<style lang="scss" scoped></style>
