<template>
  <TresGroup
    :position="
      [
        -CELL_SIZE_X * Math.floor(islandViewerStore.hakoniwa.width / 2),
        0,
        -CELL_SIZE_X * Math.floor(islandViewerStore.hakoniwa.height / 2)
      ] as Vector3
    ">
    <template v-for="cell of props.terrain.cells">
      <IslandEditorCell
        :position="
          [
            cell.data.point.x * (CELL_SIZE_X + EDGE_WIDTH_X) + ((((cell.data.point.y + 1) % 2) - 1) * CELL_SIZE_X) / 2,
            0,
            cell.data.point.y * (CELL_SIZE_X + EDGE_WIDTH_X)
          ] as Vector3
        "
        :scale="models[cell.type][cell.data.sub_type ?? 'default'].scene.scale.x"
        :cell="cell"
        :scene="models[cell.type][cell.data.sub_type ?? 'default'].scene.clone()"/>
    </template>

    <!-- 選択セルカーソル -->
    <TresMesh
      ref="selectedBox"
      :scale="selectedBoxScale"
      :position="selectedBoxPosition"
      :visible="islandEditorStore.showPlanWindow">
      <TresBoxGeometry :args="[1, 1, 1]"/>
      <template v-for="borderLine of borderLines">
        <TresMesh :scale="borderLine.scale" :position="borderLine.position">
          <TresBoxGeometry :args="[1, 1, 1]"/>
          <TresMeshBasicMaterial color="white"/>
        </TresMesh>
      </template>
    </TresMesh>

    <!-- プランセルカーソル -->
    <TresMesh
      ref="referencedBox"
      :scale="referencedBoxScale"
      :position="referencedBoxPosition"
      :visible="getReferencedPoint !== null && !islandEditorStore.isOpenPopup">
      <TresBoxGeometry :args="[1, 1, 1]"/>
      <template v-for="borderLine of borderLines">
        <TresMesh :scale="borderLine.scale" :position="borderLine.position">
          <TresBoxGeometry :args="[1, 1, 1]"/>
          <TresMeshBasicMaterial color="red"/>
        </TresMesh>
      </template>
    </TresMesh>
  </TresGroup>
</template>

<script async setup lang="ts">
import {Box3, Group, Mesh, Vector2, Vector3} from 'three'
import {useGLTF} from '@tresjs/cientos'
import IslandEditorCell from './IslandEditorCell.vue'
import {Terrain} from '$entity/Terrain'
import {computed, onMounted, shallowRef} from 'vue'
import {
  CellType,
  CELL_SIZE_X,
  getCellModels,
  getCellSubTypes,
  getCellTypes
} from '$entity/Cell.js'
import {
  EDGE_WIDTH_X,
} from '$entity/Edge.js'
import {useIslandEditorStore} from '$store/IslandEditorStore.js'
import {useIslandViewerStore} from '$store/IslandViewerStore.js'

let selectedBox = shallowRef(null)
let referencedBox = shallowRef(null)

const islandEditorStore = useIslandEditorStore()
const islandViewerStore = useIslandViewerStore()

interface Props {
  terrain: Terrain
}

const props = defineProps<Props>()

const borderLines = [
  {scale: [0.1, 1, 0.1] as Vector3, position: [-0.5, 0, -0.5] as Vector3},
  {scale: [0.1, 1, 0.1] as Vector3, position: [0.5, 0, -0.5] as Vector3},
  {scale: [0.1, 1, 0.1] as Vector3, position: [-0.5, 0, 0.5] as Vector3},
  {scale: [0.1, 1, 0.1] as Vector3, position: [0.5, 0, 0.5] as Vector3},
  {scale: [1, 0.05, 0.1] as Vector3, position: [0, 0.475, 0.5] as Vector3},
  {scale: [1, 0.05, 0.1] as Vector3, position: [0, 0.475, -0.5] as Vector3},
  {scale: [0.1, 0.05, 1] as Vector3, position: [0.5, 0.475, 0] as Vector3},
  {scale: [0.1, 0.05, 1] as Vector3, position: [-0.5, 0.475, 0] as Vector3}
]

let models = {}
for (let type of getCellTypes()) {
  models[type] = {}
  for (let subType of getCellSubTypes(type as CellType)) {
    let paths = getCellModels(type as CellType, subType)

    for (let path of paths) {
      let model = await useGLTF(path['path'], {draco: true})
      const size = new Box3().setFromObject(model.scene).getSize(new Vector3())
      model.scene.scale.x = CELL_SIZE_X / size.x
      model.scene.scale.y = CELL_SIZE_X / size.x
      model.scene.scale.z = CELL_SIZE_X / size.x
      model.scene.children[0].position.y += (size.y * (CELL_SIZE_X / size.x) - CELL_SIZE_X) / 2

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

onMounted(() => {
  selectedBox.value.material.opacity = 0.1
  selectedBox.value.material.transparent = true

  referencedBox.value.material.opacity = 0.1
  referencedBox.value.material.transparent = true
})

const selectedBoxScale = computed(() => {
  const selectedBoxScaleMargin = 0.1
  if (islandEditorStore.selectedPoint === null) {
    return new Vector3(0, 0, 0)
  }
  return new Vector3(
    (CELL_SIZE_X + EDGE_WIDTH_X) + selectedBoxScaleMargin,
    8,
    (CELL_SIZE_X + EDGE_WIDTH_X) + selectedBoxScaleMargin
  )
})

const selectedBoxPosition = computed(() => {
  const selectedPoint = islandEditorStore.selectedPoint
  if (selectedPoint === null) {
    return new Vector3(0, 0, 0)
  }
  return new Vector3(
    selectedPoint.x * (CELL_SIZE_X + EDGE_WIDTH_X) + ((((selectedPoint.y + 1) % 2) - 1) * (CELL_SIZE_X + EDGE_WIDTH_X)) / 2,
    9,
    selectedPoint.y * (CELL_SIZE_X + EDGE_WIDTH_X)
  )
})

const getReferencedPoint = computed(() => {
  let referencedPlan = islandEditorStore.plans[islandEditorStore.selectedPlanNumber - 1]
  if (!referencedPlan.data.usePoint) {
    return null
  }
  if (referencedPlan.data.useTargetIsland && referencedPlan.data.targetIsland !== islandViewerStore.island.id) {
    return null
  }
  return new Vector2(referencedPlan.data.point.x, referencedPlan.data.point.y)
})

const referencedBoxScale = computed(() => {
  const referencedPoint = getReferencedPoint.value
  const referencedBoxScaleMargin = 0.05
  if (referencedPoint === null) {
    return new Vector3(0, 0, 0)
  }
  return new Vector3(
    (CELL_SIZE_X + EDGE_WIDTH_X) + referencedBoxScaleMargin,
    8,
    (CELL_SIZE_X + EDGE_WIDTH_X) + referencedBoxScaleMargin
  )
})

const referencedBoxPosition = computed(() => {
  const referencedPoint = getReferencedPoint.value
  if (referencedPoint === null) {
    return new Vector3(0, 0, 0)
  }
  return new Vector3(
    referencedPoint.x * (CELL_SIZE_X + EDGE_WIDTH_X) + ((((referencedPoint.y + 1) % 2) - 1) * (CELL_SIZE_X + EDGE_WIDTH_X)) / 2,
    8,
    referencedPoint.y * (CELL_SIZE_X + EDGE_WIDTH_X)
  )
})
</script>

<style lang="scss" scoped></style>
